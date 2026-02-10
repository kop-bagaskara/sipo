<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPekerjaanPrepress;

class JenisPekerjaanPrepressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisPekerjaan = [
            [
                'kode' => 'ART',
                'nama_jenis' => 'Artwork',
                'is_active' => true,
                'created_by' => 'System'
            ],
            [
                'kode' => 'PLT',
                'nama_jenis' => 'Plate',
                'is_active' => true,
                'created_by' => 'System'
            ],
            [
                'kode' => 'PRF',
                'nama_jenis' => 'Proof',
                'is_active' => true,
                'created_by' => 'System'
            ],
            [
                'kode' => 'COR',
                'nama_jenis' => 'Correction',
                'is_active' => true,
                'created_by' => 'System'
            ],
            [
                'kode' => 'FIN',
                'nama_jenis' => 'Finishing',
                'is_active' => true,
                'created_by' => 'System'
            ]
        ];

        foreach ($jenisPekerjaan as $jenis) {
            JenisPekerjaanPrepress::create($jenis);
        }
    }
}
