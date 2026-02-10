<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobPrepress;
use Carbon\Carbon;

class CleanupTestJobOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'joborder:cleanup-test {--date= : Tanggal untuk cleanup (format: Y-m-d)} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup data test job order yang bermasalah';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date') ?: date('Y-m-d');
        $force = $this->option('force');

        $this->info("🧹 Cleanup test job orders for date: {$date}");

        // Cari data test berdasarkan pattern
        $testData = JobPrepress::where('nomor_job_order', 'like', "PRP-" . Carbon::parse($date)->format('ymd') . "-%")
            ->where(function($query) {
                $query->where('customer', 'TEST')
                      ->orWhere('customer', 'LIKE', '%test%')
                      ->orWhere('catatan', 'TEST')
                      ->orWhere('catatan', 'LIKE', '%test%');
            })
            ->get();

        if ($testData->isEmpty()) {
            $this->info("✅ No test data found for {$date}");
            return 0;
        }

        $this->warn("Found {$testData->count()} test records:");
        foreach ($testData as $item) {
            $this->line("  - {$item->nomor_job_order} (ID: {$item->id}) - {$item->customer}");
        }

        if (!$force) {
            if (!$this->confirm("Do you want to delete these test records?")) {
                $this->info("Cleanup cancelled.");
                return 0;
            }
        }

        $deletedCount = 0;
        foreach ($testData as $item) {
            try {
                $item->delete(); // Soft delete
                $deletedCount++;
                $this->line("  ✅ Deleted: {$item->nomor_job_order}");
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to delete {$item->nomor_job_order}: " . $e->getMessage());
            }
        }

        $this->info("🎉 Cleanup completed! Deleted {$deletedCount} test records.");

        return 0;
    }
}
