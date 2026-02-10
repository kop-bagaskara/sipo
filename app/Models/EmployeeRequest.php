<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class EmployeeRequest extends Model
{
    use HasFactory;

    protected $connection = 'pgsql2';
    protected $table = 'tb_employee_requests';

    protected $fillable = [
        'request_number',
        'request_type',
        'employee_id',
        'supervisor_id',
        'hr_id',
        'status',
        'request_data',
        'supervisor_notes',
        'supervisor_approved_at',
        'supervisor_rejected_at',
        'hr_notes',
        'hr_approved_at',
        'hr_rejected_at',
        'notes',
        'attachment_path',
        'head_id',
        'head_approved_at',
        'head_rejected_at',
        'head_notes',
        'manager_id',
        'manager_notes',
        'manager_approved_at',
        'manager_rejected_at',
        'current_approval_order',
        'replacement_person_id',
        'replacement_person_name',
        'replacement_person_nip',
        'replacement_person_position',
        'general_id',
        'general_approved_at',
        'general_rejected_at',
        'general_notes'
    ];

    protected $casts = [
        'request_data' => 'array',
        'supervisor_approved_at' => 'datetime',
        'supervisor_rejected_at' => 'datetime',
        'hr_approved_at' => 'datetime',
        'hr_rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'head_approved_at' => 'datetime',
        'head_rejected_at' => 'datetime',
        'manager_approved_at' => 'datetime',
        'manager_rejected_at' => 'datetime',
        'general_approved_at' => 'datetime',
        'general_rejected_at' => 'datetime',
        'current_approval_order' => 'integer'
    ];

    // Request types
    const TYPE_SHIFT_CHANGE = 'shift_change';
    const TYPE_ABSENCE = 'absence';
    const TYPE_OVERTIME = 'overtime';
    const TYPE_VEHICLE_ASSET = 'vehicle_asset';

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SUPERVISOR_APPROVED = 'supervisor_approved';
    const STATUS_SUPERVISOR_REJECTED = 'supervisor_rejected';
    const STATUS_HEAD_APPROVED = 'head_approved';
    const STATUS_HEAD_REJECTED = 'head_rejected';
    const STATUS_MANAGER_APPROVED = 'manager_approved';
    const STATUS_MANAGER_REJECTED = 'manager_rejected';
    const STATUS_HR_APPROVED = 'hr_approved';
    const STATUS_HR_REJECTED = 'hr_rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Boot method to generate request number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = $model->generateRequestNumber();
            }
            // Set current_approval_order to 0 when request is created
            if (is_null($model->current_approval_order)) {
                $model->current_approval_order = 0;
            }
        });
    }

    /**
     * Generate unique request number
     * Format: HRD-251105-0001 (HRD-tahun(2 digit)bulan(2 digit)hari(2 digit)-counter(4 digit))
     */
    public function generateRequestNumber()
    {
        $prefix = 'HRD-';
        $year = date('y'); // 2 digit tahun (25 untuk 2025)
        $month = date('m'); // 2 digit bulan (11 untuk November)
        $day = date('d');   // 2 digit hari (05 untuk tanggal 5)
        $datePart = $year . $month . $day; // Format: 251105

        // Cari request terakhir dengan tanggal yang sama
        $lastRequest = static::whereYear('created_at', date('Y'))
                          ->whereMonth('created_at', date('m'))
                          ->whereDay('created_at', date('d'))
                          ->where('request_number', 'LIKE', $prefix . $datePart . '-%')
                          ->orderBy('id', 'desc')
                          ->first();

        // Parse counter dari nomor request terakhir
        $sequence = 1;
        if ($lastRequest && $lastRequest->request_number) {
            // Format: HRD-251105-0001
            // Ambil 4 digit terakhir setelah tanda '-'
            $parts = explode('-', $lastRequest->request_number);
            if (count($parts) >= 3) {
                // Ambil bagian terakhir (counter)
                $lastSequence = intval($parts[count($parts) - 1]);
                $sequence = $lastSequence + 1;
            } elseif (count($parts) === 2) {
                // Fallback: ambil 4 digit terakhir dari string
                $lastSequence = intval(substr($lastRequest->request_number, -4));
                if ($lastSequence > 0) {
                    $sequence = $lastSequence + 1;
                }
            }
        }

        return $prefix . $datePart . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the employee who made the request
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the supervisor who needs to approve
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get the HR who handles the request
     */
    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    /**
     * Get the manager who handles the request
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the head who handles the request
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Get the General Manager who handles the request (for Manager-created requests)
     */
    public function general()
    {
        return $this->belongsTo(User::class, 'general_id');
    }

    /**
     * Get replacement person (pelaksana tugas)
     */
    public function replacementPerson()
    {
        return $this->belongsTo(User::class, 'replacement_person_id');
    }

    /**
     * Get notifications for this request
     */
    // TODO: Relasi notifications bisa ditambahkan nanti

    /**
     * Get asset usage logs for this request
     */
    // TODO: Relasi asset usage logs bisa ditambahkan nanti

    /**
     * Get overtime employees for this request (for overtime requests)
     */
    public function overtimeEmployees()
    {
        return $this->hasMany(\App\Models\OvertimeEmployee::class, 'request_id');
    }

    /**
     * Scope for pending supervisor approval
     */
    public function scopePendingSupervisor($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for pending HR approval
     */
    public function scopePendingHR($query)
    {
        return $query->where('status', self::STATUS_SUPERVISOR_APPROVED);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_HR_APPROVED);
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->whereIn('status', [self::STATUS_SUPERVISOR_REJECTED, self::STATUS_HR_REJECTED]);
    }

    /**
     * Scope for requests by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    /**
     * Scope for requests by employee
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope for requests by supervisor
     */
    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    /**
     * Check if request can be approved by supervisor
     */
    public function canBeApprovedBySupervisor()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request can be approved by HR
     */
    public function canBeApprovedByHR()
    {
        // HR can approve if:
        // 1. Status is not rejected/cancelled
        // 2. HR hasn't approved/rejected yet
        // 3. current_approval_order indicates we're at HR level or before it
        // 4. Untuk request dari Manager: General Manager harus sudah approve

        // Check if already rejected/cancelled
        if ($this->status === self::STATUS_SUPERVISOR_REJECTED ||
            $this->status === self::STATUS_HR_REJECTED ||
            $this->status === self::STATUS_HR_APPROVED ||
            $this->status === self::STATUS_CANCELLED ||
            $this->status === self::STATUS_MANAGER_REJECTED) {
            return false;
        }

        // Check if HR already approved/rejected
        if (!is_null($this->hr_approved_at) || !is_null($this->hr_rejected_at)) {
            return false;
        }

        // KHUSUS UNTUK REQUEST DARI MANAGER: Cek apakah General Manager sudah approve
        if ($this->employee && (int) $this->employee->jabatan === 3) {
            // Request dari Manager: harus General Manager approve dulu baru HR bisa approve
            if (is_null($this->general_approved_at)) {
                return false; // General Manager belum approve
            }
            if (!is_null($this->general_rejected_at)) {
                return false; // General Manager sudah reject
            }
            // General Manager sudah approve, HR bisa approve
            return true;
        }

        // KHUSUS UNTUK REQUEST DARI HEAD PRODUKSI (jabatan 4, divisi 4): Cek apakah General Manager sudah approve
        // Karena Manager Produksi tidak ada, approval langsung ke General Manager
        if ($this->employee && (int) $this->employee->jabatan === 4 && (int) $this->employee->divisi === 4) {
            // Request dari HEAD PRODUKSI: harus General Manager approve dulu baru HR bisa approve
            if (is_null($this->general_approved_at)) {
                return false; // General Manager belum approve
            }
            if (!is_null($this->general_rejected_at)) {
                return false; // General Manager sudah reject
            }
            // General Manager sudah approve, HR bisa approve
            return true;
        }

        // Get approval flow to find HR position
        // Untuk absence, gunakan divisi pemohon
        $divisi = ($this->request_type === self::TYPE_ABSENCE && $this->employee) ? $this->employee->divisi : null;
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($this->request_type, $divisi);
        $hrSetting = null;
        foreach ($approvalFlow as $setting) {
            if ($setting->role_key === 'hr') {
                $hrSetting = $setting;
                break;
            }
        }

        if (!$hrSetting) {
            return false;
        }

        $hrOrder = $hrSetting->approval_order;
        $currentOrder = $this->current_approval_order ?? 0;

        // Logika berbeda antara shift_change dan absence
        if ($this->request_type === self::TYPE_SHIFT_CHANGE) {
            // Untuk shift_change: HR hanya bisa approve jika current_approval_order
            // sudah mencapai level sebelum HR (semua approver sebelum HR sudah approve)
            // Contoh: SPV(1) -> HEAD(2) -> HR(3)
            // HR bisa approve jika current_order == 2 (HEAD sudah approve)
            return $currentOrder == ($hrOrder - 1);
        } else {
            // Untuk absence: gunakan logika lama (current_order >= 1)
            return $currentOrder >= 1 && $currentOrder < $hrOrder;
        }
    }

    // public function canBeApprovedByHR()
    // {
    //     // HR can approve if:
    //     // 1. Status is not rejected/cancelled
    //     // 2. HR hasn't approved/rejected yet
    //     // 3. current_approval_order indicates we're at HR level or before it
    //     // 4. Untuk request dari Manager: General Manager harus sudah approve

    //     // Check if already rejected/cancelled
    //     if ($this->status === self::STATUS_SUPERVISOR_REJECTED ||
    //         $this->status === self::STATUS_HR_REJECTED ||
    //         $this->status === self::STATUS_HR_APPROVED ||
    //         $this->status === self::STATUS_CANCELLED ||
    //         $this->status === self::STATUS_MANAGER_REJECTED) {
    //         return false;
    //     }

    //     // Check if HR already approved/rejected
    //     if (!is_null($this->hr_approved_at) || !is_null($this->hr_rejected_at)) {
    //         return false;
    //     }

    //     // KHUSUS UNTUK REQUEST DARI MANAGER: Cek apakah General Manager sudah approve
    //     if ($this->employee && (int) $this->employee->jabatan === 3) {
    //         // dd($this->employee);
    //         // Request dari Manager: harus General Manager approve dulu baru HR bisa approve
    //         if (is_null($this->general_approved_at)) {
    //             return false; // General Manager belum approve
    //         }
    //         if (!is_null($this->general_rejected_at)) {
    //             return false; // General Manager sudah reject
    //         }
    //         // General Manager sudah approve, HR bisa approve
    //         return true;
    //     }

    //     // Get approval flow to find HR position
    //     // Untuk absence, gunakan divisi pemohon
    //     $divisi = ($this->request_type === self::TYPE_ABSENCE && $this->employee) ? $this->employee->divisi : null;
    //     $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($this->request_type, $divisi);
    //     $hrSetting = null;
    //     foreach ($approvalFlow as $setting) {
    //         if ($setting->role_key === 'hr') {
    //             $hrSetting = $setting;
    //             break;
    //         }
    //     }

    //     if (!$hrSetting) {
    //         return false;
    //     }

    //     $hrOrder = $hrSetting->approval_order;
    //     $currentOrder = $this->current_approval_order ?? 0;

    //     // HR can approve if current_approval_order >= 1 AND current_approval_order < hrOrder
    //     // This means all previous levels (up to current_order) have been approved
    //     return $currentOrder >= 1 && $currentOrder < $hrOrder;
    // }

    /**
     * Check if request can be approved by manager
     */
    public function canBeApprovedByManager()
    {
        // Manager can approve if:
        // 1. Status is pending AND manager_approved_at is null
        // 2. OR previous approvers (based on approval order) have approved
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        // Check if manager already approved/rejected
        if ($this->manager_approved_at !== null || $this->manager_rejected_at !== null) {
            return false;
        }

        return true;
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_SUPERVISOR_APPROVED]);
    }

    /**
     * Get request type label
     */
    public function getRequestTypeLabelAttribute()
    {
        $labels = [
            self::TYPE_SHIFT_CHANGE => 'Permohonan Tukar Shift',
            self::TYPE_ABSENCE => 'Permohonan Tidak Masuk Kerja',
            self::TYPE_OVERTIME => 'Surat Perintah Lembur',
            self::TYPE_VEHICLE_ASSET => 'Permintaan Membawa Kendaraan/Inventaris'
        ];

        return $labels[$this->request_type] ?? $this->request_type;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        // PRIORITAS 1: Cek khusus untuk request dari Manager: General Manager -> HRD
        // Jika General Manager sudah approve, next approver adalah HRD
        if (!is_null($this->general_approved_at)) {
            if (!is_null($this->hr_approved_at)) {
                return 'Disetujui HRD';
            } elseif (!is_null($this->hr_rejected_at)) {
                return 'Ditolak HRD';
            } else {
                return 'Menunggu Approval HRD';
            }
        }

        // PRIORITAS 2: Jika General Manager sudah ditunjuk tapi belum approve/reject
        if (!is_null($this->general_id) && is_null($this->general_approved_at) && is_null($this->general_rejected_at)) {
            return 'Menunggu Approval GENERAL MANAGER';
        }

        // Untuk absence, gunakan divisi pemohon untuk approval flow
        $divisi = ($this->request_type === self::TYPE_ABSENCE && $this->employee) ? $this->employee->divisi : null;

        // If status is pending, check which approver is next
        if ($this->status === self::STATUS_PENDING) {
            // PRIORITAS: Cek General Manager dulu sebelum mencari next approver dari flow
            if (!is_null($this->general_approved_at)) {
                if (!is_null($this->hr_approved_at)) {
                    return 'Disetujui HRD';
                } elseif (!is_null($this->hr_rejected_at)) {
                    return 'Ditolak HRD';
                } else {
                    return 'Menunggu Approval HRD';
                }
            }

            if (!is_null($this->general_id) && is_null($this->general_approved_at) && is_null($this->general_rejected_at)) {
                return 'Menunggu Approval GENERAL MANAGER';
            }

            // Get approval flow to determine next approver
            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($this->request_type, $divisi);
            $currentOrder = $this->current_approval_order ?? 0;

            // Find next approver
            $nextApprover = null;
            foreach ($approvalFlow as $setting) {
                if ($setting->approval_order > $currentOrder) {
                    $nextApprover = $setting;
                    break;
                }
            }

            if ($nextApprover) {
                if ($nextApprover->role_key === 'hr') {
                    return 'Menunggu Approval HR';
                } elseif ($nextApprover->role_key === 'head_division') {
                    if ($this->request_type === self::TYPE_SHIFT_CHANGE) {
                        return 'Menunggu Approval HEAD DIVISI/MANAGER';
                    } else {
                        return 'Menunggu Approval HEAD DIVISI';
                    }
                } elseif ($nextApprover->role_key === 'manager') {
                    return 'Menunggu Approval MANAGER';
                } elseif ($nextApprover->role_key === 'spv_division') {
                    return 'Menunggu Approval SPV';
                } elseif ($nextApprover->role_key === 'general_manager') {
                    return 'Menunggu Approval GENERAL MANAGER';
                }
            }

            return 'Menunggu Approval Atasan';
        }

        // For approved statuses, check next approver in flow
        if ($this->status === self::STATUS_SUPERVISOR_APPROVED ||
            $this->status === self::STATUS_MANAGER_APPROVED) {

            // PRIORITAS: Cek General Manager dulu sebelum mencari next approver dari flow
            if (!is_null($this->general_approved_at)) {
                if (!is_null($this->hr_approved_at)) {
                    return 'Disetujui HRD';
                } elseif (!is_null($this->hr_rejected_at)) {
                    return 'Ditolak HRD';
                } else {
                    return 'Menunggu Approval HRD';
                }
            }

            if (!is_null($this->general_id) && is_null($this->general_approved_at) && is_null($this->general_rejected_at)) {
                return 'Menunggu Approval GENERAL MANAGER';
            }

            // Get approval flow to determine next approver
            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($this->request_type, $divisi);
            $currentOrder = $this->current_approval_order ?? 0;

            // Find next approver
            $nextApprover = null;
            foreach ($approvalFlow as $setting) {
                if ($setting->approval_order > $currentOrder) {
                    $nextApprover = $setting;
                    break;
                }
            }

            if ($nextApprover) {
                if ($nextApprover->role_key === 'hr') {
                    return 'Menunggu Approval HRD';
                } elseif ($nextApprover->role_key === 'manager') {
                    return 'Menunggu Approval Manager';
                } elseif ($nextApprover->role_key === 'head_division') {
                    if ($this->request_type === self::TYPE_SHIFT_CHANGE) {
                        return 'Menunggu Approval HEAD DIVISI/MANAGER';
                    } else {
                        return 'Menunggu Approval HEAD DIVISI';
                    }
                } elseif ($nextApprover->role_key === 'spv_division') {
                    return 'Menunggu Approval SPV';
                } elseif ($nextApprover->role_key === 'general_manager') {
                    return 'Menunggu Approval GENERAL MANAGER';
                }
            }

            // Ultimate fallback: jika tidak ada next approver, return current status
            return $this->status;
        }

        $labels = [
            self::STATUS_SUPERVISOR_REJECTED => 'Ditolak Atasan',
            self::STATUS_MANAGER_REJECTED => 'Ditolak Manager',
            self::STATUS_HR_APPROVED => 'Disetujui HR',
            self::STATUS_HR_REJECTED => 'Ditolak HR',
            self::STATUS_CANCELLED => 'Dibatalkan'
        ];

        // Jika status tidak ditemukan di labels, gunakan dynamic check
        if (!isset($labels[$this->status])) {
            // PRIORITAS: Cek General Manager dulu sebelum mencari next approver dari flow
            if (!is_null($this->general_approved_at)) {
                if (!is_null($this->hr_approved_at)) {
                    return 'Disetujui HRD';
                } elseif (!is_null($this->hr_rejected_at)) {
                    return 'Ditolak HRD';
                } else {
                    return 'Menunggu Approval HRD';
                }
            }

            if (!is_null($this->general_id) && is_null($this->general_approved_at) && is_null($this->general_rejected_at)) {
                return 'Menunggu Approval GENERAL MANAGER';
            }

            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($this->request_type, $divisi);
            $currentOrder = $this->current_approval_order ?? 0;

            // Cari next approver
            $nextApprover = null;
            foreach ($approvalFlow as $setting) {
                if ($setting->approval_order > $currentOrder) {
                    $nextApprover = $setting;
                    break;
                }
            }

            if ($nextApprover) {
                if ($nextApprover->role_key === 'hr') {
                    return 'Menunggu Approval HRD';
                } elseif ($nextApprover->role_key === 'manager') {
                    return 'Menunggu Approval Manager';
                } elseif ($nextApprover->role_key === 'head_division') {
                    if ($this->request_type === self::TYPE_SHIFT_CHANGE) {
                        return 'Menunggu Approval HEAD DIVISI/MANAGER';
                    } else {
                        return 'Menunggu Approval HEAD DIVISI';
                    }
                } elseif ($nextApprover->role_key === 'spv_division') {
                    return 'Menunggu Approval SPV';
                } elseif ($nextApprover->role_key === 'general_manager') {
                    return 'Menunggu Approval GENERAL MANAGER';
                }
            }
        }

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        // PRIORITAS 1: Cek khusus untuk request dari Manager: General Manager -> HRD
        // Jika General Manager sudah approve, next approver adalah HRD
        if (!is_null($this->general_approved_at)) {
            if (!is_null($this->hr_approved_at)) {
                return 'badge-success';
            } elseif (!is_null($this->hr_rejected_at)) {
                return 'badge-danger';
            } else {
                return 'badge-warning';
            }
        }

        // PRIORITAS 2: Jika General Manager sudah ditunjuk tapi belum approve/reject
        if (!is_null($this->general_id) && is_null($this->general_approved_at) && is_null($this->general_rejected_at)) {
            return 'badge-warning';
        }

        $classes = [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_SUPERVISOR_APPROVED => 'badge-info',
            self::STATUS_SUPERVISOR_REJECTED => 'badge-danger',
            self::STATUS_MANAGER_APPROVED => 'badge-info',
            self::STATUS_MANAGER_REJECTED => 'badge-danger',
            self::STATUS_HR_APPROVED => 'badge-success',
            self::STATUS_HR_REJECTED => 'badge-danger',
            self::STATUS_CANCELLED => 'badge-secondary'
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }

    /**
     * Get days since request was created
     */
    public function getDaysSinceCreatedAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Get formatted request data for display - sesuai dengan form kertas
     */
    public function getFormattedRequestDataAttribute()
    {
        $data = $this->request_data;

        switch ($this->request_type) {
            case self::TYPE_SHIFT_CHANGE:
                $scenarioType = $data['scenario_type'] ?? 'exchange';

                // Determine scenario label
                $scenarioLabels = [
                    'self' => 'Tukar Shift Diri Sendiri',
                    'exchange' => 'Tukar Shift dengan Rekan Kerja',
                    'holiday' => 'Tukar Shift karena Hari Merah (Lembur)',
                ];
                $scenarioLabel = isset($scenarioLabels[$scenarioType]) ? $scenarioLabels[$scenarioType] : 'Tukar Shift';

                $formatted = [
                    'Jenis Tukar Shift' => $scenarioLabel,
                    'Nama Pemohon' => $data['applicant_name'] ?? '',
                    'Bagian Pemohon' => $data['applicant_department'] ?? '',
                ];

                if ($scenarioType === 'self') {
                    // Scenario 1: Self shift change
                    $formatted['Hari/Tanggal'] = $data['date'] ?? '';
                    $formatted['Jam Saat Ini'] = ($data['original_start_time'] ?? '') . ' - ' . ($data['original_end_time'] ?? '');
                    $formatted['Jam Baru'] = ($data['new_start_time'] ?? '') . ' - ' . ($data['new_end_time'] ?? '');
                    $formatted['Keperluan'] = $data['purpose'] ?? '';
                } elseif ($scenarioType === 'exchange') {
                    // Scenario 2: Exchange with colleague
                    $formatted['Hari/Tanggal'] = $data['date'] ?? '';
                    $formatted['Jam Pemohon'] = ($data['applicant_start_time'] ?? '') . ' - ' . ($data['applicant_end_time'] ?? '');
                    $formatted['Keperluan'] = $data['purpose'] ?? '';
                    $formatted['Nama Pengganti'] = $data['substitute_name'] ?? '';
                    $formatted['Bagian Pengganti'] = $data['substitute_department'] ?? '';
                    $formatted['Jam Pengganti'] = ($data['substitute_start_time'] ?? '') . ' - ' . ($data['substitute_end_time'] ?? '');
                    $formatted['Keperluan Pengganti'] = $data['substitute_purpose'] ?? '';
                } elseif ($scenarioType === 'holiday') {
                    // Scenario 3: Holiday work / compensatory leave
                    $formatted['Tanggal Kerja (Hari Merah)'] = $data['holiday_work_date'] ?? '';
                    $formatted['Jam Kerja'] = ($data['applicant_start_time'] ?? '') . ' - ' . ($data['applicant_end_time'] ?? '');
                    $formatted['Total Jam Kerja'] = ($data['work_hours'] ?? '') . ' jam';
                    $formatted['Tanggal Pengganti (OFF)'] = $data['compensatory_date'] ?? '';
                    $formatted['Keperluan'] = $data['purpose'] ?? '';
                }

                return $formatted;

            case self::TYPE_ABSENCE:
                // Sesuai form "PERMOHONAN TIDAK MASUK KERJA"
                return [
                    'Nama' => $data['name'] ?? '',
                    'Bagian' => $data['department'] ?? '',
                    'Jenis Cuti' => $data['absence_type'] ?? '',
                    'Selama' => $data['duration_days'] ?? '',
                    'Tanggal' => $data['date_range'] ?? '',
                    'Keperluan' => $data['purpose'] ?? '',
                    'Sisa Cuti Tahunan' => $data['remaining_annual_leave'] ?? ''
                ];

            case self::TYPE_OVERTIME:
                // Sesuai form "SURAT PERINTAH LEMBUR"
                return [
                    'Hari/Tanggal' => $data['date'] ?? '',
                    'Lokasi' => $data['location'] ?? '',
                    'Nama Karyawan' => $data['employee_name'] ?? '',
                    'Bagian' => $data['department'] ?? '',
                    'Jam Mulai' => $data['start_time'] ?? '',
                    'Jam Selesai' => $data['end_time'] ?? '',
                    'Keterangan Pekerjaan' => $data['job_description'] ?? ''
                ];

            case self::TYPE_VEHICLE_ASSET:
                // Sesuai form "PERMINTAAN MEMBAWA KENDARAAN/INVENTARIS"
                return [
                    'Nama' => $data['name'] ?? '',
                    'Bagian' => $data['department'] ?? '',
                    'Jenis Kendaraan/Barang' => $data['vehicle_item_type'] ?? '',
                    'No. Pol' => $data['license_plate'] ?? '',
                    'Keperluan' => $data['purpose_type'] ?? '', // Dinas/Pengiriman/Pribadi
                    'Uraian' => $data['description'] ?? '',
                    'Tujuan' => $data['destination'] ?? '',
                    'Tanggal' => $data['date'] ?? ''
                ];

            default:
                return $data;
        }
    }

    /**
     * Get current approval status text based on approval flow
     * Returns who is currently waiting to approve
     */
    public function getCurrentApprovalStatusTextAttribute(): string
    {
        // Check each approval level in order
        if ($this->status === self::STATUS_CANCELLED) {
            return '<span class="badge badge-secondary">Dibatalkan</span>';
        }

        // PRIORITAS 1: Cek khusus untuk request dari Manager: General Manager -> HRD
        // Jika General Manager sudah approve, next approver adalah HRD
        if (!is_null($this->general_approved_at)) {
            if (!is_null($this->hr_approved_at)) {
                return '<span class="badge badge-success">Disetujui HRD</span>';
            } elseif (!is_null($this->hr_rejected_at)) {
                return '<span class="badge badge-danger">Ditolak HRD</span>';
            } else {
                return '<span class="badge badge-info">Menunggu HRD</span>';
            }
        }

        // PRIORITAS 2: Jika General Manager sudah ditunjuk tapi belum approve/reject
        if (!is_null($this->general_id) && is_null($this->general_approved_at) && is_null($this->general_rejected_at)) {
            return '<span class="badge badge-warning">Menunggu GENERAL MANAGER</span>';
        }

        // Check rejections first
        if ($this->supervisor_rejected_at) {
            return '<span class="badge badge-danger">Ditolak SPV</span>';
        }
        if ($this->head_rejected_at) {
            return '<span class="badge badge-danger">Ditolak HEAD</span>';
        }
        if ($this->manager_rejected_at) {
            return '<span class="badge badge-danger">Ditolak Manager</span>';
        }
        if ($this->general_rejected_at) {
            return '<span class="badge badge-danger">Ditolak GENERAL MANAGER</span>';
        }
        if ($this->hr_rejected_at) {
            return '<span class="badge badge-danger">Ditolak HR</span>';
        }

        // Check approvals to determine current status
        if ($this->hr_approved_at) {
            return '<span class="badge badge-success">Disetujui HR</span>';
        }

        // Untuk absence, gunakan divisi pemohon untuk approval flow
        $divisi = ($this->request_type === self::TYPE_ABSENCE && $this->employee) ? $this->employee->divisi : null;

        // Get approval flow to determine next approver
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($this->request_type, $divisi);
        $currentOrder = $this->current_approval_order ?? 0;

        // Find next approver based on approval flow
        $nextApprover = null;
        foreach ($approvalFlow as $setting) {
            if ($setting->approval_order > $currentOrder) {
                $nextApprover = $setting;
                break;
            }
        }

        if ($nextApprover) {
            if ($nextApprover->role_key === 'hr') {
                return '<span class="badge badge-info">Menunggu HR</span>';
            } elseif ($nextApprover->role_key === 'manager') {
                return '<span class="badge badge-info">Menunggu Manager</span>';
            } elseif ($nextApprover->role_key === 'head_division') {
                return '<span class="badge badge-warning">Menunggu HEAD</span>';
            } elseif ($nextApprover->role_key === 'spv_division') {
                return '<span class="badge badge-primary">Menunggu SPV</span>';
            } elseif ($nextApprover->role_key === 'general_manager') {
                return '<span class="badge badge-warning">Menunggu GENERAL MANAGER</span>';
            }
        }

        // Fallback: default waiting for supervisor
        return '<span class="badge badge-primary">Menunggu SPV</span>';
    }

    /**
     * Get detailed approval flow status
     * Returns array of all approval statuses
     */
    public function getApprovalFlowStatus(): array
    {
        return [
            'spv' => [
                'approved' => !is_null($this->supervisor_approved_at),
                'rejected' => !is_null($this->supervisor_rejected_at),
                'at' => $this->supervisor_approved_at ?? $this->supervisor_rejected_at,
            ],
            'head' => [
                'approved' => !is_null($this->head_approved_at),
                'rejected' => !is_null($this->head_rejected_at),
                'at' => $this->head_approved_at ?? $this->head_rejected_at,
            ],
            'manager' => [
                'approved' => !is_null($this->manager_approved_at),
                'rejected' => !is_null($this->manager_rejected_at),
                'at' => $this->manager_approved_at ?? $this->manager_rejected_at,
            ],
            'hr' => [
                'approved' => !is_null($this->hr_approved_at),
                'rejected' => !is_null($this->hr_rejected_at),
                'at' => $this->hr_approved_at ?? $this->hr_rejected_at,
            ],
        ];
    }
}
