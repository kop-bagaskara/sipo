<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterCutyKhususCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Pernikahan Karyawan',
                'default_duration_days' => 3,
                'description' => 'Cuti khusus untuk pernikahan karyawan sendiri',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Mengkhitankan/Membaptis Anak',
                'default_duration_days' => 2,
                'description' => 'Cuti untuk mengkhitankan atau membaptis anak',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Pernikahan Anak Karyawan',
                'default_duration_days' => 2,
                'description' => 'Cuti untuk pernikahan anak karyawan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Kematian Anak/Suami/Istri',
                'default_duration_days' => 2,
                'description' => 'Cuti untuk duka cita atas meninggalnya anak, suami, atau istri',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Kematian Ayah/Ibu/Mertua/Menantu',
                'default_duration_days' => 2,
                'description' => 'Cuti untuk duka cita atas meninggalnya ayah, ibu, mertua, atau menantu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Kematian Orang Serumah',
                'default_duration_days' => 1,
                'description' => 'Cuti untuk duka cita atas meninggalnya orang serumah',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Persalinan Istri/Keguguran',
                'default_duration_days' => 2,
                'description' => 'Cuti untuk mendampingi istri yang akan melahirkan atau keguguran',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Lainnya',
                'default_duration_days' => 1,
                'description' => 'Cuti khusus dengan kategori lain (isi keterangan)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('master_cuty_khusus_categories')->insert($categories);
    }
}
