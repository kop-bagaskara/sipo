<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisiApprovalSetting extends Model
{

    protected $connection = 'pgsql2';
    protected $table = 'tb_divisi_approval_settings';


    protected $fillable = [
        'divisi_id',
        'spv_enabled',
        'head_enabled',
        'manager_enabled',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'spv_enabled' => 'boolean',
        'head_enabled' => 'boolean',
        'manager_enabled' => 'boolean',
    ];

    // Relations
    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    // Get approval chain as array (dynamic - cari user berdasarkan role di divisi yang sama)
    public function getChainAttribute()
    {
        $chain = [];

        // SPV - cari user dengan jabatan SPV (5) di divisi yang sama
        if ($this->spv_enabled) {
            $spv = User::on('pgsql')->where('divisi', $this->divisi_id)
                ->where('jabatan', 5) // SPV
                ->first();

            $chain['spv'] = [
                'user_id' => $spv ? $spv->id : null,
                'level_name' => 'SPV',
                'user' => $spv
            ];
        }

        // HEAD - cari user dengan jabatan HEAD (4) di divisi yang sama
        if ($this->head_enabled) {
            $head = User::on('pgsql')->where('divisi', $this->divisi_id)
                ->where('jabatan', 4) // HEAD
                ->first();

            $chain['head'] = [
                'user_id' => $head ? $head->id : null,
                'level_name' => 'HEAD',
                'user' => $head
            ];
        }

        // MANAGER - cari user dengan jabatan MANAGER (3) di divisi yang sama
        if ($this->manager_enabled) {
            $manager = User::on('pgsql')->where('divisi', $this->divisi_id)
                ->where('jabatan', 3) // MANAGER
                ->first();

            $chain['manager'] = [
                'user_id' => $manager ? $manager->id : null,
                'level_name' => 'MANAGER',
                'user' => $manager
            ];
        }

        // HRD selalu ada (hardcoded atau dari config)
        $hrd = User::on('pgsql')->where('divisi', 7)->first();
        $chain['hrd'] = [
            'user_id' => $hrd ? $hrd->id : null,
            'level_name' => 'HRD',
            'user' => $hrd
        ];

        return $chain;
    }

    // Get ordered approval levels only
    public function getApprovalLevelsAttribute()
    {
        $levels = [];

        if ($this->spv_enabled) $levels[] = 'spv';
        if ($this->head_enabled) $levels[] = 'head';
        if ($this->manager_enabled) $levels[] = 'manager';
        $levels[] = 'hrd'; // HRD selalu ada

        return $levels;
    }
}
