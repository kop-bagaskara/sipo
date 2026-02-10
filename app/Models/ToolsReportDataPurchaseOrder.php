<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToolsReportDataPurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'tls_reportpo';
    protected $guarded = ['id'];

    public function kodeMaterialSIM()
    {
        return $this->belongsTo(KodeMaterialSIM::class, 'kode_material', 'Code');
    }
}
