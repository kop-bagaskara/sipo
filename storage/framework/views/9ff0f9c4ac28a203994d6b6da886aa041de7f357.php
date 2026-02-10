<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Order Prepress Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .job-details {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        .detail-value {
            color: #212529;
            text-align: right;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .priority-urgent {
            background-color: #dc3545;
            color: white;
        }
        .priority-normal {
            background-color: #28a745;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .btn:hover {
            background-color: #5a6fd8;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🚀 Job Order Prepress Baru</h1>
            <p>Notifikasi otomatis dari sistem SiP Krisanthium</p>
        </div>

        <p>Halo <strong><?php echo e($user->name); ?></strong>,</p>
        
        <p>Sebuah job order prepress baru telah dibuat dan memerlukan perhatian Anda.</p>

        <div class="job-details">
            <h3 style="margin-top: 0; color: #667eea;">📋 Detail Job Order</h3>
            
            <div class="detail-row">
                <span class="detail-label">Kode Design:</span>
                <span class="detail-value"><strong><?php echo e($jobOrder->kode_design); ?></strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Product:</span>
                <span class="detail-value"><?php echo e($jobOrder->product); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span class="detail-value"><?php echo e($jobOrder->customer); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Job Type:</span>
                <span class="detail-value"><?php echo e($jobOrder->job_order); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Quantity:</span>
                <span class="detail-value">
                    <?php if(is_numeric($jobOrder->qty_order_estimation)): ?>
                        <?php echo e(number_format((float)$jobOrder->qty_order_estimation)); ?>

                    <?php else: ?>
                        <?php echo e($jobOrder->qty_order_estimation ?? '-'); ?>

                    <?php endif; ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Tanggal Order:</span>
                <span class="detail-value"><?php echo e(\Carbon\Carbon::parse($jobOrder->tanggal_job_order)->format('d/m/Y')); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Deadline:</span>
                <span class="detail-value">
                    <?php if($jobOrder->tanggal_deadline): ?>
                        <?php echo e(\Carbon\Carbon::parse($jobOrder->tanggal_deadline)->format('d/m/Y')); ?>

                    <?php else: ?>
                        -
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Priority:</span>
                <span class="detail-value">
                    <span class="priority-badge priority-<?php echo e(strtolower($jobOrder->prioritas_job)); ?>">
                        <?php echo e($jobOrder->prioritas_job); ?>

                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Created By:</span>
                <span class="detail-value"><?php echo e($jobOrder->created_by); ?></span>
            </div>
        </div>

        <?php if($jobOrder->catatan): ?>
        <div class="job-details">
            <h4 style="margin-top: 0; color: #667eea;">📝 Catatan:</h4>
            <p style="margin: 0; font-style: italic;"><?php echo e($jobOrder->catatan); ?></p>
        </div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn">Lihat Dashboard</a>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem SiP Krisanthium.</p>
            <p>Jika ada pertanyaan, silakan hubungi tim IT.</p>
            <p><small>© <?php echo e(date('Y')); ?> SiP Krisanthium. All rights reserved.</small></p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/emails/job-order-prepress-notification.blade.php ENDPATH**/ ?>