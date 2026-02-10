<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ApprovalSetting extends Model
{
    use HasFactory;

    protected $table = 'tb_approval_hr_settings';
    protected $connection = 'pgsql2';

    protected $fillable = [
        'request_type',
        'approval_level',
        'approval_order',
        'approver_type',
        'role_key',
        'allowed_jabatan',
        'user_id',
        'user_name',
        'user_position',
        'is_active',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approval_order' => 'integer',
        'allowed_jabatan' => 'array'
    ];

    /**
     * Boot method to clear cache on model changes
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when approval setting is saved (created or updated)
        static::saved(function ($model) {
            static::clearApprovalFlowCache($model->request_type);
        });

        // Clear cache when approval setting is deleted
        static::deleted(function ($model) {
            static::clearApprovalFlowCache($model->request_type);
        });
    }

    /**
     * Get the user that owns the approval setting
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific request type
     */
    public function scopeForRequestType($query, $requestType)
    {
        return $query->where('request_type', $requestType);
    }

    /**
     * Scope for specific approval level
     */
    public function scopeForApprovalLevel($query, $approvalLevel)
    {
        return $query->where('approval_level', $approvalLevel);
    }

    /**
     * Get approval settings for a specific request type ordered by approval order
     * Cached for 1 hour to improve performance
     *
     * @param string $requestType Request type (absence, shift_change, overtime, vehicle_asset)
     * @param int|null $divisiId Division ID (required for absence type to check division-specific settings)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getApprovalFlow($requestType, $divisiId = null)
    {
        // dd($requestType, $divisiId);
        // HARDCODE: PREPRESS (3) menggunakan alur yang sama dengan PRODUKSI (4)
        if ($divisiId === 3) {
            $divisiId = 4;
        }

        // Untuk ABSENCE, cek dulu setting per divisi
        if ($requestType === 'absence' ) {
            $cacheKey = "approval_flow_{$requestType}_divisi_{$divisiId}";

            // return Cache::remember($cacheKey, 3600, function () use ($requestType, $divisiId) {
                // Cek setting per divisi
                $divisiSetting = DivisiApprovalSetting::where('divisi_id', $divisiId)
                    ->where('is_active', true)
                    ->first();

                // dd($divisiSetting);

                // Jika ada setting divisi yang aktif, build approval flow dari setting divisi
                if ($divisiSetting) {
                    $approvalFlow = collect();
                    $approvalOrder = 1;

                    // SPV Division
                    if ($divisiSetting->spv_enabled) {
                        $approvalFlow->push(new ApprovalSetting([
                            'request_type' => $requestType,
                            'approval_level' => 'spv_division',
                            'approval_order' => $approvalOrder++,
                            'approver_type' => 'role',
                            'role_key' => 'spv_division',
                            'allowed_jabatan' => [5], // SPV
                            'user_id' => null,
                            'user_name' => 'SPV per Divisi',
                            'user_position' => 'SPV',
                            'is_active' => true,
                            'description' => 'SPV per Divisi'
                        ]));
                    }

                    // HEAD Division
                    if ($divisiSetting->head_enabled) {
                        $approvalFlow->push(new ApprovalSetting([
                            'request_type' => $requestType,
                            'approval_level' => 'head_division',
                            'approval_order' => $approvalOrder++,
                            'approver_type' => 'role',
                            'role_key' => 'head_division',
                            'allowed_jabatan' => [3, 4, 5], // MANAGER, HEAD, SPV
                            'user_id' => null,
                            'user_name' => 'HEAD per Divisi',
                            'user_position' => 'HEAD/MANAGER/SPV',
                            'is_active' => true,
                            'description' => 'HEAD per Divisi'
                        ]));
                    }

                    // Manager
                    if ($divisiSetting->manager_enabled) {
                        $approvalFlow->push(new ApprovalSetting([
                            'request_type' => $requestType,
                            'approval_level' => 'manager',
                            'approval_order' => $approvalOrder++,
                            'approver_type' => 'role',
                            'role_key' => 'manager',
                            'allowed_jabatan' => [3], // MANAGER
                            'user_id' => null,
                            'user_name' => 'Manager',
                            'user_position' => 'MANAGER',
                            'is_active' => true,
                            'description' => 'Manager'
                        ]));
                    }

                    // HR (selalu ada di akhir)
                    $approvalFlow->push(new ApprovalSetting([
                        'request_type' => $requestType,
                        'approval_level' => 'hr',
                        'approval_order' => $approvalOrder++,
                        'approver_type' => 'role',
                        'role_key' => 'hr',
                        'allowed_jabatan' => null,
                        'user_id' => null,
                        'user_name' => 'Semua HRD',
                        'user_position' => 'HRD',
                        'is_active' => true,
                        'description' => 'HRD'
                    ]));

                    return $approvalFlow;
                }

                // Jika tidak ada setting divisi, fallback ke global setting
                return self::active()
                    ->forRequestType($requestType)
                    ->orderBy('approval_order')
                    ->get();
            // });
        }

        // Untuk request type lain (bukan absence) atau jika divisiId null
        $cacheKey = "approval_flow_{$requestType}";

        return Cache::remember($cacheKey, 3600, function () use ($requestType) {
            return self::active()
                ->forRequestType($requestType)
                ->orderBy('approval_order')
                ->get();
        });
    }

    /**
     * Clear cache for approval flow
     * Should be called when approval settings are updated or deleted
     */
    public static function clearApprovalFlowCache($requestType = null)
    {
        if ($requestType) {
            // Clear specific request type cache
            Cache::forget("approval_flow_{$requestType}");
        } else {
            // Clear all approval flow caches
            $requestTypes = ['absence', 'shift_change', 'overtime', 'vehicle_asset', 'spl'];
            foreach ($requestTypes as $type) {
                Cache::forget("approval_flow_{$type}");
            }
        }
    }

    /**
     * Get next approver for a specific request type and current approval level
     */
    public static function getNextApprover($requestType, $currentApprovalOrder = 0)
    {
        return self::active()
            ->forRequestType($requestType)
            ->where('approval_order', '>', $currentApprovalOrder)
            ->orderBy('approval_order')
            ->first();
    }

    /**
     * Get approver display name (for role-based or user-based)
     */
    public function getApproverDisplayNameAttribute(): string
    {
        if ($this->approver_type === 'role') {
            if ($this->role_key === 'hr') {
                return 'Semua HRD';
            } elseif ($this->role_key === 'head_division') {
                return 'HEAD per Divisi';
            } elseif ($this->role_key === 'spv_division') {
                return 'SPV per Divisi';
            }
            return ucfirst($this->role_key ?? 'Role');
        }
        return $this->user_name ?? '-';
    }

    /**
     * Get approver position display (for role-based or user-based)
     */
    public function getApproverPositionDisplayAttribute(): string
    {
        if ($this->approver_type === 'role') {
            if ($this->role_key === 'hr') {
                return 'HRD';
            } elseif ($this->role_key === 'head_division') {
                return 'HEAD/MANAGER/SPV';
            } elseif ($this->role_key === 'spv_division') {
                return 'SPV';
            }
            return ucfirst($this->role_key ?? '-');
        }
        return $this->user_position ?? '-';
    }

    /**
     * Get role_key with fallback to approval_level if role_key is null
     * Untuk backward compatibility dengan data lama yang mungkin belum punya role_key
     */
    public function getRoleKeyAttribute($value)
    {
        // Jika role_key sudah ada, gunakan itu
        if (!empty($value)) {
            return $value;
        }

        // Jika role_key null, coba mapping dari approval_level
        $approvalLevel = $this->attributes['approval_level'] ?? null;
        if ($approvalLevel) {
            // Mapping approval_level ke role_key
            $mapping = [
                'manager' => 'manager',
                'hr' => 'hr',
                'hrd' => 'hr',
                'head' => 'head_division',
                'head_division' => 'head_division',
                'supervisor' => 'spv_division',
                'spv' => 'spv_division',
                'spv_division' => 'spv_division',
            ];

            return $mapping[strtolower($approvalLevel)] ?? $approvalLevel;
        }

        return $value;
    }

    /**
     * Determine whether a given user can approve this setting row
     */
    public function isUserAllowedToApprove(User $user, array $context = []): bool
    {
        // dd($user, $this, $context);
        if ($this->approver_type === 'user') {
            return (int) $this->user_id === (int) $user->id;
        }

        if ($this->approver_type === 'role') {
            if (!empty($this->allowed_jabatan) && is_array($this->allowed_jabatan)) {
                if (!in_array((int) $user->jabatan, $this->allowed_jabatan)) {
                    return false;
                }

                // If division check is needed (for division-based roles)
                if (in_array($this->role_key, ['head_division', 'spv_division'])) {
                    $requesterDivision = $context['requester_divisi'] ?? null;
                    // dd($requesterDivision);
                    if ($requesterDivision !== null) {
                        // EXCEPTION: Head Divisi Produksi (4) bisa approve request dari Prepress (3)
                        if ((int) $user->divisi === 4 && (int) $requesterDivision === 3) {
                            return true;
                        }
                        return (int) $user->divisi === (int) $requesterDivision;
                    }
                }

                return true;
            }

            // Fallback to old role_key logic (for backward compatibility)
            if ($this->role_key === 'hr') {
                return method_exists($user, 'isHR') ? $user->isHR() : false;
            }
            if ($this->role_key === 'manager') {
                // Maps to jabatan: MANAGER (3)
                // Check if user is manager and same division as requester
                if ((int) $user->jabatan !== 3) {
                    return false;
                }
                $requesterDivision = $context['requester_divisi'] ?? null;
                if ($requesterDivision !== null) {
                    return (int) $user->divisi === (int) $requesterDivision;
                }
                return true; // fallback if context not provided
            }
            if ($this->role_key === 'head_division') {
                // Must be head/manager/supervisor AND same division as requester
                // Maps to jabatan: MANAGER (3), HEAD (4), SPV (5)
                $isHead = method_exists($user, 'canApprove') ? $user->canApprove() : false;
                $requesterDivision = $context['requester_divisi'] ?? null;
                if ($requesterDivision === null) {
                    return $isHead; // fallback if context not provided
                }

                // EXCEPTION: Head Divisi Produksi (4) bisa approve request dari Prepress (3)
                if ((int) $user->divisi === 4 && (int) $requesterDivision === 3) {
                    return $isHead;
                }

                return $isHead && ((int) $user->divisi === (int) $requesterDivision);
            }
            if ($this->role_key === 'spv_division') {
                // Maps to jabatan: SPV (5)
                // Check if user is SPV and same division as requester
                if ((int) $user->jabatan !== 5) {
                    return false;
                }
                $requesterDivision = $context['requester_divisi'] ?? null;
                if ($requesterDivision !== null) {
                    return (int) $user->divisi === (int) $requesterDivision;
                }
                return true; // fallback if context not provided
            }
        }

        return false;
    }
}
