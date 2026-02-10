<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingItem extends Model
{
    protected $table = 'tb_mapping_items';

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'kode', 'nama_barang', 'jumlah', 'panjang', 'lebar', 'gramasi', 'kg_per_pcs',
    //     'pcs_dos', 'speed', 'target', 'tipe_job', 'optimal', 'information', 'jumlah_warna',
    //     'jumlah_t1', 'jumlah_t2', 'jumlah_t3', 'jumlah_t4', 'jumlah_t5', 'jumlah_t6', 'jumlah_t7',
    //     't1', 't2', 't3', 't4', 't5', 't6', 't7',
    //     'coating1', 'coating2', 'dimensi1', 'dimensi2', 'dimensi3',
    // ];
}
