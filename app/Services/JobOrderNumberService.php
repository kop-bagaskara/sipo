<?php

namespace App\Services;

use App\Models\JobPrepress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Added DB facade
use Illuminate\Support\Facades\Log; // Added Log facade

class JobOrderNumberService
{
    /**
     * Generate nomor job order otomatis dengan format PRP-YYMMDD-XXXX
     *
     * @param string|null $tanggal Tanggal job order (format Y-m-d)
     * @return string
     */
    public function generateJobOrderNumber($tanggal = null)
    {
        // Gunakan tanggal hari ini jika tidak ada tanggal yang diberikan
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();

        // Format: PRP-YYMMDD-XXXX
        $year = $date->format('y'); // 2 digit tahun
        $month = $date->format('m'); // 2 digit bulan
        $day = $date->format('d'); // 2 digit tanggal

        $prefix = "PRP-{$year}{$month}{$day}-";

        // Gunakan database sequence atau counter yang lebih reliable
        // Cari counter terakhir dengan query yang lebih robust
        $lastJobOrder = DB::select("
            SELECT nomor_job_order
            FROM tb_job_prepresses
            WHERE nomor_job_order LIKE ?
            ORDER BY CAST(SUBSTRING(nomor_job_order FROM '([0-9]{4})$') AS INTEGER) DESC
            LIMIT 1
        ", [$prefix . '%']);

        if (!empty($lastJobOrder)) {
            // Extract counter dari nomor terakhir
            $lastCounter = (int) substr($lastJobOrder[0]->nomor_job_order, -4);
            $newCounter = $lastCounter + 1;
        } else {
            // Jika belum ada job order untuk tanggal ini, mulai dari 0001
            $newCounter = 1;
        }

        // Format counter dengan leading zeros (4 digit)
        $formattedCounter = str_pad($newCounter, 4, '0', STR_PAD_LEFT);

        return "{$prefix}{$formattedCounter}";
    }

    /**
     * Validasi apakah nomor job order sudah ada
     *
     * @param string $nomorJobOrder
     * @return bool
     */
    public function isJobOrderNumberExists($nomorJobOrder)
    {
        return JobPrepress::where('nomor_job_order', $nomorJobOrder)
            ->whereNull('deleted_at')  // Exclude soft deleted
            ->exists();
    }

    /**
     * Cek dan cleanup nomor job order yang duplicate
     * Berguna untuk maintenance jika ada data yang bermasalah
     *
     * @param string|null $tanggal Tanggal untuk cek (default: hari ini)
     * @return array
     */
    public function checkDuplicateJobOrderNumbers($tanggal = null)
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $prefix = "PRP-" . $date->format('ymd') . "-";

        // Cari semua nomor job order untuk tanggal tertentu (termasuk soft deleted)
        $allJobOrders = JobPrepress::where('nomor_job_order', 'like', $prefix . '%')
            ->orderBy('nomor_job_order')
            ->get();

        $duplicates = [];
        $seen = [];

        foreach ($allJobOrders as $jobOrder) {
            $nomor = $jobOrder->nomor_job_order;

            if (in_array($nomor, $seen)) {
                $duplicates[] = [
                    'id' => $jobOrder->id,
                    'nomor_job_order' => $nomor,
                    'is_deleted' => !is_null($jobOrder->deleted_at),
                    'deleted_at' => $jobOrder->deleted_at,
                    'created_at' => $jobOrder->created_at,
                ];
            } else {
                $seen[] = $nomor;
            }
        }

        return [
            'date' => $date->format('Y-m-d'),
            'prefix' => $prefix,
            'total_records' => $allJobOrders->count(),
            'active_records' => $allJobOrders->whereNull('deleted_at')->count(),
            'deleted_records' => $allJobOrders->whereNotNull('deleted_at')->count(),
            'duplicates' => $duplicates,
            'has_duplicates' => !empty($duplicates),
        ];
    }

    /**
     * Generate nomor job order yang aman (dengan retry jika ada duplicate)
     *
     * @param string|null $tanggal Tanggal job order (format Y-m-d)
     * @param int $maxRetries Maksimal retry untuk generate nomor unik
     * @return string
     */
    public function generateSafeJobOrderNumber($tanggal = null, $maxRetries = 10)
    {
        $attempts = 0;
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $prefix = "PRP-" . $date->format('ymd') . "-";

        while ($attempts < $maxRetries) {
            try {
                // Gunakan database transaction dengan locking untuk mencegah race condition
                $nomor = DB::transaction(function () use ($date, $prefix) {
                    // Lock table untuk mencegah concurrent access
                    $lastJobOrder = DB::select("
                        SELECT nomor_job_order
                        FROM tb_job_prepresses
                        WHERE nomor_job_order LIKE ?
                        ORDER BY CAST(SUBSTRING(nomor_job_order FROM '([0-9]{4})$') AS INTEGER) DESC
                        LIMIT 1
                        FOR UPDATE
                    ", [$prefix . '%']);

                    if (!empty($lastJobOrder)) {
                        // Extract counter dari nomor terakhir
                        $lastCounter = (int) substr($lastJobOrder[0]->nomor_job_order, -4);
                        $newCounter = $lastCounter + 1;
                    } else {
                        // Jika belum ada job order untuk tanggal ini, mulai dari 0001
                        $newCounter = 1;
                    }

                    // Format counter dengan leading zeros (4 digit)
                    $formattedCounter = str_pad($newCounter, 4, '0', STR_PAD_LEFT);
                    $nomor = "{$prefix}{$formattedCounter}";

                    // Double check: pastikan nomor ini benar-benar unik
                    $exists = DB::select("SELECT 1 FROM tb_job_prepresses WHERE nomor_job_order = ? LIMIT 1", [$nomor]);
                    if (!empty($exists)) {
                        throw new \Exception("Nomor {$nomor} sudah ada, perlu retry");
                    }

                    return $nomor;
                }, 5); // 5 retry untuk transaction

                // Jika berhasil generate, return
                if ($nomor) {
                    return $nomor;
                }

            } catch (\Exception $e) {
                $attempts++;

                // Log error untuk debugging
                Log::warning("Failed to generate job order number, attempt {$attempts}: " . $e->getMessage());

                if ($attempts >= $maxRetries) {
                    throw new \Exception("Tidak bisa generate nomor job order unik setelah {$maxRetries} percobaan: " . $e->getMessage());
                }

                // Tunggu sebentar sebelum retry untuk mengurangi race condition
                usleep(100000); // 0.1 detik
            }
        }

        // Fallback: generate dengan timestamp untuk memastikan unik
        $timestamp = time();
        $randomSuffix = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        return "{$prefix}{$timestamp}{$randomSuffix}";
    }
}
