<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Lembur - <?php echo e($splRequest->spl_number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4ecdc4;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4ecdc4;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header h2 {
            color: #666;
            margin: 5px 0;
            font-size: 18px;
        }
        .info-section {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .info-label {
            display: table-cell;
            width: 25%;
            font-weight: bold;
            color: #666;
        }
        .info-value {
            display: table-cell;
            width: 75%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #4ecdc4;
            color: white;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 20px;
        }
        .signature-box {
            border: 1px solid #ddd;
            padding: 10px;
            min-height: 100px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media  print {
            body {
                margin: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT PERINTAH LEMBUR (SPL)</h1>
        <h2>PT. KRISANTHIUM OFFSET PRINTING</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No. SPL:</div>
            <div class="info-value"><strong><?php echo e($splRequest->spl_number); ?></strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">
                <?php if($splRequest->request_date): ?>
                    <?php echo e(\Carbon\Carbon::parse($splRequest->request_date)->format('d F Y')); ?>

                <?php else: ?>
                    -
                <?php endif; ?>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Shift:</div>
            <div class="info-value"><?php echo e($splRequest->shift); ?></div>
        </div>
        <?php if(!empty($startTime) || !empty($endTime)): ?>
        <div class="info-row">
            <div class="info-label">Jam:</div>
            <div class="info-value">
                <?php if(!empty($startTime) && !empty($endTime)): ?>
                    <?php echo e(date('H:i', strtotime($startTime))); ?> - <?php echo e(date('H:i', strtotime($endTime))); ?>

                <?php elseif(!empty($startTime)): ?>
                    Mulai: <?php echo e(date('H:i', strtotime($startTime))); ?>

                <?php elseif(!empty($endTime)): ?>
                    Selesai: <?php echo e(date('H:i', strtotime($endTime))); ?>

                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if($splRequest->mesin): ?>
        <div class="info-row">
            <div class="info-label">Mesin:</div>
            <div class="info-value"><?php echo e($splRequest->mesin); ?></div>
        </div>
        <?php endif; ?>
        <div class="info-row">
            <div class="info-label">Keperluan:</div>
            <div class="info-value"><?php echo e($splRequest->keperluan); ?></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                
                <th style="width: 25%;">Nama Karyawan</th>
                <th style="width: 10%;">Jam Mulai</th>
                <th style="width: 10%;">Jam Selesai</th>
                <th style="width: 12%;">Tanda Tangan</th>
                
                <th style="width: 15%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $splRequest->employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                    
                    <td><?php echo e($employee->employee_name); ?></td>
                    <td style="text-align: center;">
                        <?php if($splRequest->request_date): ?>
                            <?php echo e(\Carbon\Carbon::parse($splRequest->request_date)->format('d/m/Y')); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if(!empty($startTime)): ?>
                            <?php echo e(date('H:i', strtotime($startTime))); ?>

                        <?php else: ?>

                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if(!empty($endTime)): ?>
                            <?php echo e(date('H:i', strtotime($endTime))); ?>

                        <?php else: ?>

                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="signature-box">
                            <?php if($employee->is_signed): ?>
                                <small style="color: green;">✓ Sudah TTD</small>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </div>
                    </td>
                    

                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-cell">
                <div class="signature-box">
                    <br><br>
                </div>
                <strong>Supervisor</strong><br>
                <small><?php echo e($splRequest->supervisor->name ?? '-'); ?></small>
            </div>
            <div class="signature-cell">
                <div class="signature-box">
                    <br><br>
                </div>
                <strong>Mengetahui,<br>Head Divisi</strong>
            </div>
            <div class="signature-cell">
                <div class="signature-box">
                    <br><br>
                </div>
                <strong>Menyetujui,<br>HRD</strong>
                <?php if($splRequest->hrd): ?>
                    <br><small><?php echo e($splRequest->hrd->name); ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Dicetak pada: <?php echo e(now()->format('d F Y H:i:s')); ?></p>
    </div>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/spl/print.blade.php ENDPATH**/ ?>