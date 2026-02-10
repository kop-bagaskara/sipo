<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobPrepress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HardDeleteDuplicateJobOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'joborder:hard-delete-duplicates {--date= : Tanggal untuk cleanup (format: Y-m-d)} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hard delete data job order yang duplicate untuk cleanup total';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date') ?: date('Y-m-d');
        $force = $this->option('force');

        $this->info("🗑️  Hard delete duplicate job orders for date: {$date}");

        // Cari data yang duplicate berdasarkan nomor_job_order
        $duplicates = DB::select("
            SELECT nomor_job_order, COUNT(*) as count,
                   array_agg(id) as ids,
                   array_agg(created_at) as created_ats
            FROM tb_job_prepresses
            WHERE nomor_job_order LIKE ?
            GROUP BY nomor_job_order
            HAVING COUNT(*) > 1
        ", ["PRP-" . Carbon::parse($date)->format('ymd') . "-%"]);

        if (empty($duplicates)) {
            $this->info("✅ No duplicates found for {$date}");
            return 0;
        }

        $this->warn("Found " . count($duplicates) . " duplicate numbers:");
        $totalToDelete = 0;

        foreach ($duplicates as $duplicate) {
            $this->line("  - {$duplicate->nomor_job_order} (Count: {$duplicate->count})");
            $totalToDelete += ($duplicate->count - 1); // Keep 1, delete the rest
        }

        $this->warn("Total records to delete: {$totalToDelete}");

        if (!$force) {
            if (!$this->confirm("Do you want to hard delete these duplicate records? This action cannot be undone!")) {
                $this->info("Hard delete cancelled.");
                return 0;
            }
        }

        $deletedCount = 0;

        foreach ($duplicates as $duplicate) {
            $ids = $duplicate->ids;
            $createdAts = $duplicate->created_ats;

            // Sort by created_at to keep the oldest record
            $sortedData = array_combine($ids, $createdAts);
            asort($sortedData);

            $idsToKeep = array_keys(array_slice($sortedData, 0, 1, true));
            $idsToDelete = array_diff($ids, $idsToKeep);

            $this->line("  Keeping ID: " . implode(', ', $idsToKeep));
            $this->line("  Deleting IDs: " . implode(', ', $idsToDelete));

            foreach ($idsToDelete as $idToDelete) {
                try {
                    // Hard delete dengan raw query
                    DB::delete("DELETE FROM tb_job_prepresses WHERE id = ?", [$idToDelete]);
                    $deletedCount++;
                    $this->line("    ✅ Hard deleted ID: {$idToDelete}");
                } catch (\Exception $e) {
                    $this->error("    ❌ Failed to delete ID {$idToDelete}: " . $e->getMessage());
                }
            }
        }

        $this->info("🎉 Hard delete completed! Deleted {$deletedCount} duplicate records.");
        $this->info("⚠️  Note: These records were permanently deleted (not soft deleted).");

        return 0;
    }
}
