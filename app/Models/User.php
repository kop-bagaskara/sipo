<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'pgsql';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',

    // ];

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'divisi',
        'jabatan',
        'level',
        'supervisor_id',
        'is_hr',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the username attribute
     */
    public function getUsernameAttribute()
    {
        return $this->attributes['username'] ?? $this->email;
    }

    /**
     * Set the username attribute
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = $value;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function divisiUser()
    {
        return $this->belongsTo(Divisi::class, 'divisi');
    }
    public function jabatanUser()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan');
    }
    public function levelUser()
    {
        return $this->belongsTo(Level::class, 'level');
    }

    // HR System Relations
    public function employeeRequests()
    {
        return $this->hasMany(EmployeeRequest::class, 'employee_id');
    }

    public function supervisedRequests()
    {
        return $this->hasMany(EmployeeRequest::class, 'supervisor_id');
    }

    public function hrRequests()
    {
        return $this->hasMany(EmployeeRequest::class, 'hr_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function hrNotifications()
    {
        return $this->hasMany(\App\Models\HrNotification::class, 'recipient_id');
    }

    public function assetUsageLogs()
    {
        return $this->hasMany(AssetUsageLog::class, 'employee_id');
    }

    // Training System Relations
    public function trainingParticipants()
    {
        return $this->hasMany(TrainingParticipant::class, 'employee_id');
    }

    public function createdTrainings()
    {
        return $this->hasMany(TrainingMaster::class, 'created_by');
    }

    public function updatedTrainings()
    {
        return $this->hasMany(TrainingMaster::class, 'updated_by');
    }

    // HR System Scopes
    public function scopeIsHR($query)
    {
        return $query->where('is_hr', true);
    }

    public function scopeHasSupervisor($query)
    {
        return $query->whereNotNull('supervisor_id');
    }

    public function scopeSubordinatesOf($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    // HR System Helper Methods
    public function isHR()
    {
        // Cek divisi HRD (divisi 7)
        if ($this->divisi == 7) {
            return true;
        }

        // Cek field is_hr sebagai fallback
        return $this->is_hr ?? false;
    }

    public function hasSupervisor()
    {
        // Cek jabatan HEAD (4) dan MANAGER (3) untuk approval
        if ($this->jabatan == 4 || $this->jabatan == 3 || $this->jabatan == 5) {
            return true;
        }

        return !is_null($this->supervisor_id);
    }

    public function canApprove()
    {
        // dd($this->jabatan);
        // HEAD (jabatan 4) dan MANAGER (jabatan 3) bisa approve
        return $this->jabatan == 4 || $this->jabatan == 3 || $this->jabatan == 5;
    }

    public function getUnreadNotificationsCount()
    {
        return $this->hrNotifications()->unread()->count();
    }

    /**
     * Get remaining annual leave from MySQL7 database
     */
    public function getRemainingAnnualLeave()
    {
        // dd($this->name);
        try {
            $today = date('Y-m-d');

            // Cari NIP di masteremployee berdasarkan nama user
            $employee = DB::connection('mysql7')
                ->table('masteremployee')
                ->where('nama', $this->name)
                ->whereDate('begda', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('endda')
                      ->orWhereDate('endda', '>=', $today);
                })
                ->first();

            // dd($employee->Nip);

            if (!$employee) {
                return 0;
            }

            // Cari di mastercuti berdasarkan NIP dengan periode aktif (begda <= today && (endda is null || endda >= today))
            $cuti = DB::connection('mysql7')
                ->table('mastercuti')
                ->where('Nip', (int)$employee->Nip)
                // ->whereDate('begda', '<=', $today)
                // ->where(function ($q) use ($today) {
                //     $q->whereNull('endda')
                //       ->orWhereDate('endda', '>=', $today);
                // })
                ->orderByDesc('endda')
                ->first();

            // dd($cuti);

            return $cuti ? (int) $cuti->Quota : 0;
        } catch (\Exception $e) {
            Log::error('Error getting remaining annual leave: ' . $e->getMessage());
            return 0;
        }
    }

    public function getPendingApprovalsCount()
    {
        if ($this->isHR()) {
            return $this->hrRequests()->pendingHR()->count();
        } elseif ($this->hasSupervisor()) {
            return $this->supervisedRequests()->pendingSupervisor()->count();
        }

        return 0;
    }

    /**
     * Get Kode Group from masteremployee (mysql7 database)
     */
    public function getKodeGroup()
    {
        try {
            $employee = DB::connection('mysql7')
                ->table('masteremployee')
                ->where('Nama', $this->name)
                ->first();

            if (!$employee) {
                Log::warning('Employee not found in masteremployee: ' . $this->name);
                return null;
            }

            // Cek beberapa kemungkinan nama field (case insensitive, dengan spasi, dll)
            $kodeGroup = $employee->kode_group ?? $employee->{'Kode Group'} ?? $employee->{'kode_group'} ?? null;

            if ($kodeGroup === null) {
                Log::warning('Kode Group not found for employee: ' . $this->name . '. Available fields: ' . json_encode(array_keys((array)$employee)));
            }

            return $kodeGroup;
        } catch (\Exception $e) {
            Log::error('Error getting kode_group: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Work Group data from masteremployee and masterworkgroup (mysql7 database)
     */
    public function getWorkGroupData()
    {
        try {
            $workGroupData = DB::connection('mysql7')
                ->table('masteremployee')
                ->leftJoin('masterworkgroup', 'masteremployee.Kode Group', '=', 'masterworkgroup.Kode Group')
                ->where('masteremployee.Nama', $this->name)
                ->select(
                    'masteremployee.Kode Group',
                    'masterworkgroup.Nama Group',
                    'masterworkgroup.WorkingDays'
                )
                ->first();

            // dd($workGroupData);

            if (!$workGroupData || !$workGroupData->{'Kode Group'}) {
                Log::warning('Work Group data not found for employee: ' . $this->name);
                return null;
            }

            return $workGroupData;
        } catch (\Exception $e) {
            Log::error('Error getting work_group data: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get masteremployee data from mysql7 database
     */
    public function getMasterEmployeeData()
    {
        try {
            return DB::connection('mysql7')
                ->table('masteremployee')
                ->where('nama', $this->name)
                ->first();
        } catch (\Exception $e) {
            Log::error('Error getting masteremployee data: ' . $e->getMessage());
            return null;
        }
    }
}
