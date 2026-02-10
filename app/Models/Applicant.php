<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Applicant extends Model
{
    use HasFactory;

    protected $table = 'tb_applicants';
    protected $connection = 'pgsql2';

    protected $fillable = [
        // Data Posisi & Jabatan
        'posisi_dilamar',
        'gaji_diharapkan',
        'gaji_terakhir',
        'mulai_kerja',

        // Data Diri
        'nama_lengkap',
        'alias',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'kebangsaan',
        'no_ktp',

        // Alamat
        'alamat_ktp',
        'kode_pos_ktp',
        'alamat_domisili',
        'kode_pos_domisili',

        // Kontak
        'no_handphone',
        'no_npwp',
        'email',
        'bpjs_kesehatan',
        'kontak_darurat',
        'hubungan_kontak_darurat',

        // Data Tambahan (JSON)
        'pendidikan',
        'kursus',
        'pengalaman',
        'keluarga_anak',
        'keluarga_ortu',
        'bahasa',
        'sim',
        'punya_mobil',
        'punya_motor',
        'kerja_lembur',
        'kerja_shift',
        'kerja_luar_kota',
        'test_psiko',
        'test_kesehatan',
        'hobby',
        'lain_lain',
        'referensi',

        // Deklarasi
        'tanggal_deklarasi',
        'ttd_pelamar',

        // File uploads
        'cv_file',
        'foto',
        'ttd_signature',

        // Status & Tracking
        'status',
        'tanggal_melamar',
        'is_draft',
        'created_by',
        'updated_by',
        'status_staff',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_melamar' => 'date',
        'mulai_kerja' => 'date',
        'tanggal_deklarasi' => 'date',
        'is_draft' => 'boolean',
        'pendidikan' => 'array',
        'kursus' => 'array',
        'pengalaman' => 'array',
        'keluarga_anak' => 'array',
        'keluarga_ortu' => 'array',
        'bahasa' => 'array',
        'sim' => 'array',
        'referensi' => 'array',
    ];

    // Relationship dengan test results
    public function testResults()
    {
        return $this->hasMany(ApplicantTestResult::class, 'applicant_id', 'id');
    }

    // Accessor untuk status
    public function getStatusFormattedAttribute()
    {
        $status = [
            'pending' => 'Menunggu',
            'test' => 'Sedang Test',
            'interview' => 'Interview',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak'
        ];

        return $status[$this->status] ?? $this->status;
    }

    // Accessor untuk jenis kelamin
    public function getJenisKelaminFormattedAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan posisi
    public function scopeByPosition($query, $position)
    {
        return $query->where('posisi_dilamar', 'like', '%' . $position . '%');
    }

    // Method untuk menghitung total skor test
    public function getTotalTestScore()
    {
        return $this->testResults()->sum('score');
    }

    // Method untuk mendapatkan rata-rata skor test
    public function getAverageTestScore()
    {
        $totalScore = $this->getTotalTestScore();
        $testCount = $this->testResults()->count();

        return $testCount > 0 ? round($totalScore / $testCount, 2) : 0;
    }

    // Method untuk mengecek apakah semua test sudah selesai
    public function isAllTestsCompleted()
    {
        // Test Krapelin (test_2) sementara dinonaktifkan, jadi hanya 3 test yang harus selesai
        // Test yang aktif: test_1 (Matematika), test_3 (Buta Warna), test_4 (Kepribadian)
        $activeTestTypes = ['test_1', 'test_3', 'test_4'];
        $completedTests = $this->testResults()
            ->whereIn('test_type', $activeTestTypes)
            ->count();

        return $completedTests >= count($activeTestTypes);
    }
}
