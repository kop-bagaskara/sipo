<?php

namespace App\Imports;

use App\Models\EmployeeData;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class EmployeeDataImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    protected $errors = [];
    protected $successCount = 0;
    protected $skipCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Normalize header keys - handle various header formats
            $normalizedRow = $this->normalizeRow($row);

            // Skip jika NIP kosong
            if (empty($normalizedRow['nip']) || empty($normalizedRow['nama_karyawan'])) {
                $this->skipCount++;
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error normalizing row', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            $this->skipCount++;
            return null;
        }

        // Cek apakah NIP sudah ada
        $existing = EmployeeData::where('nip', $normalizedRow['nip'])->first();
        if ($existing) {
            // Update data yang sudah ada
            $this->updateEmployee($existing, $normalizedRow);
            $this->successCount++;
            return null;
        }

        // Hitung usia dari tgl_lahir jika ada
        $usia = null;
        if (!empty($normalizedRow['tgl_lahir'])) {
            try {
                $tglLahir = $this->parseDate($normalizedRow['tgl_lahir']);
                if ($tglLahir) {
                    $usia = Carbon::parse($tglLahir)->age;
                }
            } catch (\Exception $e) {
                // Skip jika format tanggal tidak valid
            }
        }

        // Hitung masa kerja dari tgl_masuk jika ada
        $masaKerja = null;
        if (!empty($normalizedRow['tgl_masuk'])) {
            try {
                $tglMasuk = $this->parseDate($normalizedRow['tgl_masuk']);
                if ($tglMasuk) {
                    $masaKerja = Carbon::parse($tglMasuk)->diffInYears(Carbon::now()) . ' tahun';
                }
            } catch (\Exception $e) {
                // Skip jika format tanggal tidak valid
            }
        }

        $this->successCount++;

        return new EmployeeData([
            'nip' => $this->truncateString($normalizedRow['nip'], 50),
            'nama_karyawan' => $this->truncateString($normalizedRow['nama_karyawan'], 255),
            'lp' => $this->truncateString($normalizedRow['lp'] ?? null, 10),
            'lvl' => $this->truncateString($normalizedRow['lvl'] ?? null, 50),
            'dept' => $this->truncateString($normalizedRow['dept'] ?? null, 100),
            'bagian' => $this->truncateString($normalizedRow['bagian'] ?? null, 100),
            'tgl_masuk' => !empty($normalizedRow['tgl_masuk']) ? $this->parseDate($normalizedRow['tgl_masuk']) : null,
            'status_update' => $this->truncateString($normalizedRow['status_update'] ?? null, 50),
            'tanggal_awal' => !empty($normalizedRow['tanggal_awal']) ? $this->parseDate($normalizedRow['tanggal_awal']) : null,
            'tanggal_berakhir' => !empty($normalizedRow['tanggal_berakhir']) ? $this->parseDate($normalizedRow['tanggal_berakhir']) : null,
            'masa_kerja' => $this->truncateString($masaKerja, 50),
            'tempat_lahir' => $this->truncateString($normalizedRow['tempat_lahir'] ?? null, 100),
            'tgl_lahir' => !empty($normalizedRow['tgl_lahir']) ? $this->parseDate($normalizedRow['tgl_lahir']) : null,
            'usia' => $usia,
            'alamat_ktp' => $normalizedRow['alamat_ktp'] ?? null, // text field, no limit
            'email' => $this->truncateString($normalizedRow['email'] ?? null, 255),
            'no_hp' => $this->truncateString($normalizedRow['no_hp'] ?? null, 20),
            'alamat_domisili' => $normalizedRow['alamat_domisili'] ?? null, // text field, no limit
            'nomor_kontak_darurat' => $this->truncateString($normalizedRow['nomor_kontak_darurat'] ?? null, 20),
            'agama' => $this->truncateString($normalizedRow['agama'] ?? null, 50),
            'pendidikan' => $this->truncateString($normalizedRow['pendidikan'] ?? null, 100),
            'jurusan' => $this->truncateString($normalizedRow['jurusan'] ?? null, 100),
            'foto_path' => null, // Foto tidak bisa diimport via Excel
        ]);
    }

    /**
     * Truncate string to specified length
     */
    protected function truncateString($value, $maxLength)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $stringValue = (string) $value;
        if (mb_strlen($stringValue) > $maxLength) {
            Log::warning("Truncating field value", [
                'original_length' => mb_strlen($stringValue),
                'max_length' => $maxLength,
                'value' => mb_substr($stringValue, 0, 50) . '...'
            ]);
            return mb_substr($stringValue, 0, $maxLength);
        }

        return $stringValue;
    }

    /**
     * Normalize row keys to handle various header formats
     */
    protected function normalizeRow(array $row)
    {
        $normalized = [];

        // Normalize semua key row menjadi lowercase untuk memudahkan matching
        $normalizedRowKeys = [];
        foreach ($row as $key => $value) {
            $normalizedKey = strtolower(trim(str_replace(['/', ' ', '-'], '_', $key)));
            $normalizedRowKeys[$normalizedKey] = $value;
        }

        // Mapping untuk berbagai format header
        // WithHeadingRow akan mengubah header menjadi lowercase dan mengganti spasi/slash dengan underscore
        // Contoh: "L/P" -> "l_p", "TANGGAL AWAL KONTRAK" -> "tanggal_awal_kontrak"
        $mappings = [
            // NIP (skip 'no' karena itu adalah nomor urut, bukan NIP)
            'nip' => ['nip'],

            // Nama Karyawan
            'nama_karyawan' => ['nama_karyawan', 'nama'],

            // LP / L/P -> menjadi "l_p" atau "lp"
            'lp' => ['lp', 'l_p', 'l/p', 'jenis_kelamin'],

            // LVL
            'lvl' => ['lvl', 'level'],

            // DEPT
            'dept' => ['dept', 'departemen'],

            // BAGIAN
            'bagian' => ['bagian', 'divisi'],

            // TGL MASUK -> menjadi "tgl_masuk"
            'tgl_masuk' => ['tgl_masuk', 'tanggal_masuk'],

            // STATUS UPDATE -> menjadi "status_update"
            'status_update' => ['status_update', 'status'],

            // TANGGAL AWAL KONTRAK -> menjadi "tanggal_awal_kontrak"
            'tanggal_awal' => ['tanggal_awal', 'tanggal_awal_kontrak', 'tgl_awal', 'tgl_awal_kontrak'],

            // TANGGAL BERAKHIR KONTRAK -> menjadi "tanggal_berakhir_kontrak"
            'tanggal_berakhir' => ['tanggal_berakhir', 'tanggal_berakhir_kontrak', 'tgl_berakhir', 'tgl_berakhir_kontrak'],

            // MASA KERJA -> menjadi "masa_kerja"
            'masa_kerja' => ['masa_kerja'],

            // TEMPAT LAHIR -> menjadi "tempat_lahir"
            'tempat_lahir' => ['tempat_lahir'],

            // TGL LAHIR -> menjadi "tgl_lahir"
            'tgl_lahir' => ['tgl_lahir', 'tanggal_lahir'],

            // USIA
            'usia' => ['usia'],

            // ALAMAT KTP -> menjadi "alamat_ktp"
            'alamat_ktp' => ['alamat_ktp', 'alamat'],

            // Email -> menjadi "email"
            'email' => ['email', 'e_mail'],

            // No HP -> menjadi "no_hp" atau "no_hp_"
            'no_hp' => ['no_hp', 'no_hp_', 'hp', 'handphone', 'telepon'],

            // ALAMAT DOMISILI -> menjadi "alamat_domisili"
            'alamat_domisili' => ['alamat_domisili', 'domisili'],

            // NOMOR KONTAK DARURAT -> menjadi "nomor_kontak_darurat"
            'nomor_kontak_darurat' => ['nomor_kontak_darurat', 'kontak_darurat', 'no_kontak_darurat'],

            // AGAMA
            'agama' => ['agama'],

            // PENDIDIKAN TERAKHIR -> menjadi "pendidikan_terakhir"
            'pendidikan' => ['pendidikan', 'pendidikan_terakhir'],

            // JURUSAN
            'jurusan' => ['jurusan'],
        ];

        // Normalize each field - gunakan normalizedRowKeys untuk matching yang lebih efisien
        foreach ($mappings as $targetKey => $sourceKeys) {
            foreach ($sourceKeys as $sourceKey) {
                $normalizedSourceKey = strtolower(trim(str_replace(['/', ' ', '-'], '_', $sourceKey)));

                // Cek di normalizedRowKeys
                if (isset($normalizedRowKeys[$normalizedSourceKey])) {
                    $value = $normalizedRowKeys[$normalizedSourceKey];
                    // Skip jika kosong atau null, tapi tetap set jika 0 atau false
                    if ($value !== null && $value !== '') {
                        $normalized[$targetKey] = $value;
                        break; // Sudah ketemu, lanjut ke field berikutnya
                    }
                }
            }
        }

        return $normalized;
    }

    /**
     * Update existing employee data
     */
    protected function updateEmployee($employee, array $row)
    {
        // Normalize row keys
        $normalizedRow = $this->normalizeRow($row);

        // Hitung usia dari tgl_lahir jika ada
        $usia = $employee->usia;
        if (!empty($normalizedRow['tgl_lahir'])) {
            try {
                $tglLahir = $this->parseDate($normalizedRow['tgl_lahir']);
                if ($tglLahir) {
                    $usia = Carbon::parse($tglLahir)->age;
                }
            } catch (\Exception $e) {
                // Keep existing usia
            }
        }

        // Hitung masa kerja dari tgl_masuk jika ada
        $masaKerja = $employee->masa_kerja;
        if (!empty($normalizedRow['tgl_masuk'])) {
            try {
                $tglMasuk = $this->parseDate($normalizedRow['tgl_masuk']);
                if ($tglMasuk) {
                    $masaKerja = Carbon::parse($tglMasuk)->diffInYears(Carbon::now()) . ' tahun';
                }
            } catch (\Exception $e) {
                // Keep existing masa_kerja
            }
        }

        $employee->update([
            'nama_karyawan' => $this->truncateString($normalizedRow['nama_karyawan'] ?? $employee->nama_karyawan, 255),
            'lp' => $this->truncateString($normalizedRow['lp'] ?? $employee->lp, 10),
            'lvl' => $this->truncateString($normalizedRow['lvl'] ?? $employee->lvl, 50),
            'dept' => $this->truncateString($normalizedRow['dept'] ?? $employee->dept, 100),
            'bagian' => $this->truncateString($normalizedRow['bagian'] ?? $employee->bagian, 100),
            'tgl_masuk' => !empty($normalizedRow['tgl_masuk']) ? $this->parseDate($normalizedRow['tgl_masuk']) : $employee->tgl_masuk,
            'status_update' => $this->truncateString($normalizedRow['status_update'] ?? $employee->status_update, 50),
            'tanggal_awal' => !empty($normalizedRow['tanggal_awal']) ? $this->parseDate($normalizedRow['tanggal_awal']) : $employee->tanggal_awal,
            'tanggal_berakhir' => !empty($normalizedRow['tanggal_berakhir']) ? $this->parseDate($normalizedRow['tanggal_berakhir']) : $employee->tanggal_berakhir,
            'masa_kerja' => $this->truncateString($masaKerja, 50),
            'tempat_lahir' => $this->truncateString($normalizedRow['tempat_lahir'] ?? $employee->tempat_lahir, 100),
            'tgl_lahir' => !empty($normalizedRow['tgl_lahir']) ? $this->parseDate($normalizedRow['tgl_lahir']) : $employee->tgl_lahir,
            'usia' => $usia,
            'alamat_ktp' => $normalizedRow['alamat_ktp'] ?? $employee->alamat_ktp, // text field, no limit
            'email' => $this->truncateString($normalizedRow['email'] ?? $employee->email, 255),
            'no_hp' => $this->truncateString($normalizedRow['no_hp'] ?? $employee->no_hp, 20),
            'alamat_domisili' => $normalizedRow['alamat_domisili'] ?? $employee->alamat_domisili, // text field, no limit
            'nomor_kontak_darurat' => $this->truncateString($normalizedRow['nomor_kontak_darurat'] ?? $employee->nomor_kontak_darurat, 20),
            'agama' => $this->truncateString($normalizedRow['agama'] ?? $employee->agama, 50),
            'pendidikan' => $this->truncateString($normalizedRow['pendidikan'] ?? $employee->pendidikan, 100),
            'jurusan' => $this->truncateString($normalizedRow['jurusan'] ?? $employee->jurusan, 100),
        ]);
    }

    /**
     * Parse date from various formats
     */
    protected function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Jika sudah berupa Carbon instance atau DateTime
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }

        // Jika sudah format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Coba parse berbagai format tanggal
        try {
            // Format Excel date (number)
            if (is_numeric($date)) {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
                return $excelDate->format('Y-m-d');
            }

            // Format Indonesia: dd/mm/yyyy atau dd-mm-yyyy
            if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date, $matches)) {
                return sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
            }

            // Format ISO: yyyy-mm-dd
            if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $date, $matches)) {
                return sprintf('%04d-%02d-%02d', $matches[1], $matches[2], $matches[3]);
            }

            // Coba parse dengan Carbon
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("Failed to parse date: {$date}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        // Validasi fleksibel - hanya validasi setelah normalisasi
        return [
            // Validasi akan dilakukan di model() setelah normalisasi
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi',
            'nama_karyawan.required' => 'Nama Karyawan wajib diisi',
        ];
    }

    /**
     * Handle failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get success count
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    /**
     * Get skip count
     */
    public function getSkipCount()
    {
        return $this->skipCount;
    }

    /**
     * Batch size - kurangi untuk menghindari memory issue
     */
    public function batchSize(): int
    {
        return 50;
    }

    /**
     * Chunk size - kurangi untuk menghindari memory issue
     */
    public function chunkSize(): int
    {
        return 50;
    }
}

