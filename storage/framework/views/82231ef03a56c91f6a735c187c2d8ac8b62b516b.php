<div class="row">
    <div class="col-12">
        <h5 class="text-primary">Pilih Skenario Tukar Shift</h5>
        <hr>
    </div>
</div>

<!-- Scenario Selection -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <label class="form-label fw-bold">Jenis Tukar Shift <span class="text-danger">*</span></label>
                <div class="mt-2">
                    <?php
                        $currentScenario = old('scenario_type', $data['scenario_type'] ?? 'exchange');
                    ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="scenario_type" id="scenario_self" value="self" <?php echo e($currentScenario === 'self' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="scenario_self">
                            <strong>Tukar Shift Diri Sendiri</strong>
                            <p class="text-muted mb-0 small">Mengubah jadwal shift sendiri (contoh: dari shift 1 07:00-15:00 menjadi 08:00-16:00)</p>
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="scenario_type" id="scenario_exchange" value="exchange" <?php echo e($currentScenario === 'exchange' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="scenario_exchange">
                            <strong>Tukar Shift dengan Rekan Kerja</strong>
                            <p class="text-muted mb-0 small">Menukar shift dengan karyawan lain (contoh: Pristiwi shift 1 dengan Marweyah shift 3)</p>
                        </label>
                    </div>
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="radio" name="scenario_type" id="scenario_holiday" value="holiday" <?php echo e($currentScenario === 'holiday' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="scenario_holiday">
                            <strong>Tukar Shift karena Hari Merah (Lembur)</strong>
                            <p class="text-muted mb-0 small">Kerja di hari merah, ditukar dengan hari OFF di tanggal lain (compensatory leave)</p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-primary">Untuk Tanggal</h5>
        <hr>
    </div>
</div>

<!-- Date Fields -->
<div class="row mb-3" id="date-section">
    <!-- Scenario 1 & 2: Single date -->
    <div class="col-md-6" id="single-date-section">
        <label class="form-label">Hari/Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="date" id="date" class="form-control <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('date', $data['date'] ?? '')); ?>" required>
        <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Scenario 3: Holiday work date and compensatory date -->
    <div class="col-md-6 d-none" id="holiday-date-section">
        <label class="form-label">Tanggal Kerja (Hari Merah) <span class="text-danger">*</span></label>
        <input type="date" name="holiday_work_date" id="holiday_work_date" class="form-control <?php $__errorArgs = ['holiday_work_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('holiday_work_date', $data['holiday_work_date'] ?? '')); ?>">
        <?php $__errorArgs = ['holiday_work_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Tanggal saat Anda bekerja di hari merah/libur</small>
    </div>

    <div class="col-md-6 d-none" id="compensatory-date-section">
        <label class="form-label">Tanggal Pengganti (OFF) <span class="text-danger">*</span></label>
        <input type="date" name="compensatory_date" id="compensatory_date" class="form-control <?php $__errorArgs = ['compensatory_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('compensatory_date', $data['compensatory_date'] ?? '')); ?>">
        <?php $__errorArgs = ['compensatory_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Tanggal pengganti untuk Anda libur (compensatory OFF)</small>
    </div>
</div>
<!-- Form Permohonan Tukar Shift -->
<div class="row mb-3" id="applicant-section">
    <div class="col-12">
        <h5 class="text-primary">Data Pemohon</h5>
        <hr>
    </div>
</div>

<div class="row mb-3" id="applicant-info-section">
    <div class="col-md-4">
        <label class="form-label">Nama <span class="text-danger">*</span></label>
        <input type="text" name="applicant_name" class="form-control <?php $__errorArgs = ['applicant_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('applicant_name', $data['applicant_name'] ?? ($data['employee_name'] ?? auth()->user()->name))); ?>" readonly>
        <?php $__errorArgs = ['applicant_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">NIP</label>
        <input type="text" name="applicant_nip" class="form-control <?php $__errorArgs = ['applicant_nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('applicant_nip', $data['applicant_nip'] ?? ($data['employee_nip'] ?? ''))); ?>" readonly>
        <?php $__errorArgs = ['applicant_nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Bagian <span class="text-danger">*</span></label>
        <input type="text" name="applicant_department"
            class="form-control <?php $__errorArgs = ['applicant_department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('applicant_department', $data['applicant_department'] ?? ($data['employee_department'] ?? ''))); ?>"
            readonly>
        <?php $__errorArgs = ['applicant_department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<!-- Scenario 1: Self shift change - Show Original and New Shift Times -->
<div class="row mb-3 d-none" id="self-shift-section">
    <div class="col-12">
        <h6 class="text-info mb-3">Detail Perubahan Shift Diri Sendiri</h6>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Mulai Saat Ini <span class="text-danger">*</span></label>
        <input type="time" name="original_start_time"
            class="form-control <?php $__errorArgs = ['original_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('original_start_time', $data['original_start_time'] ?? '')); ?>" placeholder="Contoh: 07:00">
        <?php $__errorArgs = ['original_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Jam shift saat ini</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Selesai Saat Ini <span class="text-danger">*</span></label>
        <input type="time" name="original_end_time"
            class="form-control <?php $__errorArgs = ['original_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('original_end_time', $data['original_end_time'] ?? '')); ?>" placeholder="Contoh: 15:00">
        <?php $__errorArgs = ['original_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Jam shift saat ini</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Mulai Baru <span class="text-danger">*</span></label>
        <input type="time" name="new_start_time"
            class="form-control <?php $__errorArgs = ['new_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('new_start_time', $data['new_start_time'] ?? '')); ?>" placeholder="Contoh: 08:00">
        <?php $__errorArgs = ['new_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Jam shift yang diinginkan</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Jam Selesai Baru <span class="text-danger">*</span></label>
        <input type="time" name="new_end_time"
            class="form-control <?php $__errorArgs = ['new_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('new_end_time', $data['new_end_time'] ?? '')); ?>" placeholder="Contoh: 16:00">
        <?php $__errorArgs = ['new_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Jam shift yang diinginkan</small>
    </div>
</div>

<!-- Scenario 2 & 3: Applicant Shift Times -->
<div class="row mb-3" id="applicant-shift-section">
    <div class="col-md-4">
        <label class="form-label">Jam Mulai Pemohon <span class="text-danger">*</span></label>
        <input type="time" name="applicant_start_time"
            class="form-control <?php $__errorArgs = ['applicant_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('applicant_start_time', $data['applicant_start_time'] ?? '')); ?>" required>
        <?php $__errorArgs = ['applicant_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jam Selesai Pemohon <span class="text-danger">*</span></label>
        <input type="time" name="applicant_end_time"
            class="form-control <?php $__errorArgs = ['applicant_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('applicant_end_time', $data['applicant_end_time'] ?? '')); ?>" required>
        <?php $__errorArgs = ['applicant_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Keperluan <span class="text-danger">*</span></label>
        <input type="text" name="purpose" class="form-control <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('purpose', $data['purpose'] ?? '')); ?>" placeholder="Alasan tukar shift" required>
        <?php $__errorArgs = ['purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<!-- Scenario 3: Holiday Work Hours -->
<div class="row mb-3 d-none" id="holiday-hours-section">
    <div class="col-md-6">
        <label class="form-label">Total Jam Kerja <span class="text-danger">*</span></label>
        <input type="number" name="work_hours" class="form-control <?php $__errorArgs = ['work_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('work_hours', $data['work_hours'] ?? '')); ?>" step="0.5" min="0" placeholder="Contoh: 8">
        <?php $__errorArgs = ['work_hours'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Total jam kerja di hari merah (untuk dihitung sebagai compensatory leave)</small>
    </div>
</div>

<!-- Scenario 2: Exchange with Colleague -->
<div class="row mt-5 d-none" id="substitute-section">
    <div class="col-12">
        <h5 class="text-primary">Mohon diijinkan untuk ditukar shift dengan karyawan berikut:</h5>
        <hr>
    </div>
</div>

<div class="row mb-3 d-none" id="substitute-info-section">

    <div class="col-md-6">
        <label class="form-label">Nama Pengganti <span class="text-danger">*</span></label><br>
        <input type="hidden" name="substitute_id" id="substitute_id" value="<?php echo e(old('substitute_id', $data['substitute_id'] ?? '')); ?>">
        <select name="substitute_name" id="substitute_name" class="form-control <?php $__errorArgs = ['substitute_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="width: 100%;">
            <option value="">-- Pilih Karyawan Pengganti --</option>
            <?php $__currentLoopData = $data['same_division_employees'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($employee['name']); ?>"
                        data-id="<?php echo e($employee['id']); ?>"
                        data-nip="<?php echo e($employee['nip']); ?>"
                        data-department="<?php echo e($employee['division_name']); ?>"
                        <?php echo e(old('substitute_name', $data['substitute_name'] ?? '') == $employee['name'] ? 'selected' : ''); ?>>
                    <?php echo e($employee['name']); ?> (<?php echo e($employee['nip']); ?>)
                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['substitute_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">NIP Pengganti</label>
        <input type="text" name="substitute_nip" id="substitute_nip" class="form-control <?php $__errorArgs = ['substitute_nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('substitute_nip', $data['substitute_nip'] ?? '')); ?>" readonly>
        <?php $__errorArgs = ['substitute_nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Bagian Pengganti <span class="text-danger">*</span></label>
        <input type="text" name="substitute_department" id="substitute_department"
            class="form-control <?php $__errorArgs = ['substitute_department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('substitute_department', $data['substitute_department'] ?? '')); ?>" readonly>
        <?php $__errorArgs = ['substitute_department'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<div class="row mb-3 d-none" id="substitute-shift-section">
    <div class="col-md-4">
        <label class="form-label">Jam Mulai Pengganti <span class="text-danger">*</span></label>
        <input type="time" name="substitute_start_time"
            class="form-control <?php $__errorArgs = ['substitute_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('substitute_start_time', $data['substitute_start_time'] ?? '')); ?>">
        <?php $__errorArgs = ['substitute_start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jam Selesai Pengganti <span class="text-danger">*</span></label>
        <input type="time" name="substitute_end_time"
            class="form-control <?php $__errorArgs = ['substitute_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('substitute_end_time', $data['substitute_end_time'] ?? '')); ?>">
        <?php $__errorArgs = ['substitute_end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Keperluan <span class="text-danger">*</span></label>
        <input type="text" name="substitute_purpose"
            class="form-control <?php $__errorArgs = ['substitute_purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e(old('substitute_purpose', $data['substitute_purpose'] ?? '')); ?>" placeholder="Alasan pengganti">
        <?php $__errorArgs = ['substitute_purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<style>
/* Ensure consistent height for form controls */
.form-control, select.form-control {
    height: 38px !important;
    line-height: 1.5 !important;
}

/* Specific fix for select elements */
select.form-control {
    padding: 6px 12px !important;
}

/* Ensure Select2 has same height and full width */
.select2-container {
    width: 100% !important;
}

.select2-container .select2-selection--single {
    height: 38px !important;
    line-height: 36px !important;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
}

.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}

/* Scenario selection radio buttons styling */
.form-check-input[type="radio"]:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check-label strong {
    color: #495057;
}
</style>

<script>
$(document).ready(function() {
    // Initialize Select2 for substitute dropdown with full width
    $('#substitute_name').select2({
        width: '100%'
    });

    // Scenario switching logic
    function handleScenarioChange() {
        var selectedScenario = $('input[name="scenario_type"]:checked').val();

        // Hide all sections first
        $('#single-date-section').addClass('d-none');
        $('#holiday-date-section').addClass('d-none');
        $('#compensatory-date-section').addClass('d-none');
        $('#self-shift-section').addClass('d-none');
        $('#applicant-shift-section').addClass('d-none');
        $('#holiday-hours-section').addClass('d-none');
        $('#substitute-section').addClass('d-none');
        $('#substitute-info-section').addClass('d-none');
        $('#substitute-shift-section').addClass('d-none');

        // Remove required attributes from all fields first
        $('input, select').removeAttr('required');

        if (selectedScenario === 'self') {
            // Scenario 1: Self shift change
            $('#single-date-section').removeClass('d-none');
            $('#self-shift-section').removeClass('d-none');

            // Set required fields
            $('#date').attr('required', 'required');
            $('input[name="original_start_time"]').attr('required', 'required');
            $('input[name="original_end_time"]').attr('required', 'required');
            $('input[name="new_start_time"]').attr('required', 'required');
            $('input[name="new_end_time"]').attr('required', 'required');

        } else if (selectedScenario === 'exchange') {
            // Scenario 2: Exchange with colleague
            $('#single-date-section').removeClass('d-none');
            $('#applicant-shift-section').removeClass('d-none');
            $('#substitute-section').removeClass('d-none');
            $('#substitute-info-section').removeClass('d-none');
            $('#substitute-shift-section').removeClass('d-none');

            // Set required fields
            $('#date').attr('required', 'required');
            $('input[name="applicant_start_time"]').attr('required', 'required');
            $('input[name="applicant_end_time"]').attr('required', 'required');
            $('input[name="purpose"]').attr('required', 'required');
            $('#substitute_name').attr('required', 'required');
            $('input[name="substitute_start_time"]').attr('required', 'required');
            $('input[name="substitute_end_time"]').attr('required', 'required');
            $('input[name="substitute_purpose"]').attr('required', 'required');

        } else if (selectedScenario === 'holiday') {
            // Scenario 3: Holiday work / compensatory leave
            $('#holiday-date-section').removeClass('d-none');
            $('#compensatory-date-section').removeClass('d-none');
            $('#applicant-shift-section').removeClass('d-none');
            $('#holiday-hours-section').removeClass('d-none');

            // Set required fields
            $('#holiday_work_date').attr('required', 'required');
            $('#compensatory_date').attr('required', 'required');
            $('input[name="applicant_start_time"]').attr('required', 'required');
            $('input[name="applicant_end_time"]').attr('required', 'required');
            $('input[name="purpose"]').attr('required', 'required');
            $('input[name="work_hours"]').attr('required', 'required');
        }
    }

    // Initial call to set correct visibility
    handleScenarioChange();

    // Listen for scenario changes
    $('input[name="scenario_type"]').on('change', function() {
        handleScenarioChange();
    });

    // Auto-fill NIP and Department when substitute name is selected
    $('#substitute_name').on('change', function() {
        var selectedValue = $(this).val();

        if (selectedValue && selectedValue !== '') {
            var selectedOption = $(this).find('option:selected');
            var id = selectedOption.data('id');
            var nip = selectedOption.data('nip');
            var department = selectedOption.data('department');

            if (id) {
                $('#substitute_id').val(id);
            }
            if (nip && department) {
                $('#substitute_nip').val(nip);
                $('#substitute_department').val(department);
            } else {
                $('#substitute_id').val('');
                $('#substitute_nip').val('');
                $('#substitute_department').val('');
            }
        } else {
            // Clear fields when selection is cleared
            $('#substitute_id').val('');
            $('#substitute_nip').val('');
            $('#substitute_department').val('');
        }
    });

    // Trigger change event on page load if there's a selected value
    if ($('#substitute_name').val()) {
        $('#substitute_name').trigger('change');
    }

    // Handle form submission with holiday validation
    $('form').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = new FormData(this);
        var submitButton = form.find('button[type="submit"]');
        var formMethod = form.find('input[name="_method"]').val() || 'POST';
        var isUpdate = formMethod === 'PUT' || formMethod === 'PATCH';
        var originalButtonText = submitButton.html();

        // Disable submit button to prevent double submission
        submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i>Mengirim...');

        // Determine request type
        var requestType = isUpdate ? 'PUT' : 'POST';
        var requestUrl = form.attr('action');

        $.ajax({
            url: requestUrl,
            type: requestType,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.is_holiday && !isUpdate) {
                    // Show holiday confirmation dialog with SweetAlert2 (only for create)
                    Swal.fire({
                        title: 'Konfirmasi Tanggal',
                        text: response.message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User confirmed, submit with confirmation
                            submitWithConfirmation(formData);
                        } else {
                            // User cancelled, re-enable button
                            submitButton.prop('disabled', false).html(originalButtonText);
                        }
                    });
                } else {
                    // Normal success, show SweetAlert then redirect
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message || (isUpdate ? 'Pengajuan berhasil diupdate' : 'Pengajuan berhasil dibuat'),
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = response.redirect || '<?php echo e(route("hr.requests.index")); ?>';
                    });
                }
            },
            error: function(xhr) {
                submitButton.prop('disabled', false).html(originalButtonText);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors with SweetAlert2
                    var errorMessages = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errorMessages.push(messages.join(', '));
                    });

                    Swal.fire({
                        title: 'Validasi Error',
                        html: errorMessages.join('<br>'),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    });

    function submitWithConfirmation(formData) {
        $.ajax({
            url: '<?php echo e(route("hr.requests.store.confirm")); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Show success SweetAlert then redirect
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message || 'Pengajuan berhasil dibuat',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = response.redirect || '<?php echo e(route("hr.requests.index")); ?>';
                });
            },
            error: function(xhr) {
                $('form').find('button[type="submit"]').prop('disabled', false).html('Submit');

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Handle validation errors with SweetAlert2
                    var errorMessages = [];
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        errorMessages.push(messages.join(', '));
                    });

                    Swal.fire({
                        title: 'Validasi Error',
                        html: errorMessages.join('<br>'),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/requests/forms/shift-change.blade.php ENDPATH**/ ?>