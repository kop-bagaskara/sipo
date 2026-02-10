<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialMaterialSample extends Model
{
    protected $table = 'tb_trial_material_samples';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_pengajuan',
        'tujuan_trial',
        'material_bahan',
        'kode_barang',
        'nama_barang',
        'kode_supplier',
        'nama_supplier',
        'jumlah_bahan',
        'satuan',
        'tanggal_terima',
        'deskripsi',
        'status',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchasingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchasing_user_id');
    }
    
    public function qaUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qa_user_id');
    }
    
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
    
    
    // public function processSteps(): HasMany
    // {
    //     return $this->hasMany(TrialProcessStep::class);
    // }
    
    
    // public function workflowHistory(): HasMany
    // {
    //     return $this->hasMany(TrialWorkflowHistory::class);
    // }
    
    
}
