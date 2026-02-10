<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobOrderNumberService;
use Carbon\Carbon;

class CheckJobOrderDuplicates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'joborder:check-duplicates {--date= : Tanggal untuk cek (format: Y-m-d)} {--fix : Auto fix duplicates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix duplicate job order numbers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date');
        $fix = $this->option('fix');

        $this->info('🔍 Checking for duplicate job order numbers...');

        $service = new JobOrderNumberService();
        $result = $service->checkDuplicateJobOrderNumbers($date);

        $this->info("\n=== JOB ORDER DUPLICATE CHECK ===");
        $this->info("Date: {$result['date']}");
        $this->info("Prefix: {$result['prefix']}");
        $this->info("Total Records: {$result['total_records']}");
        $this->info("Active Records: {$result['active_records']}");
        $this->info("Deleted Records: {$result['deleted_records']}");

        if ($result['has_duplicates']) {
            $this->warn("\n⚠️  DUPLICATES FOUND!");
            $this->warn("Found " . count($result['duplicates']) . " duplicate(s):");

            foreach ($result['duplicates'] as $duplicate) {
                $status = $duplicate['is_deleted'] ? '🗑️  DELETED' : '⚠️  ACTIVE';
                $this->line("  {$status} - ID: {$duplicate['id']}, Nomor: {$duplicate['nomor_job_order']}");

                if ($duplicate['is_deleted']) {
                    $this->line("    Deleted at: {$duplicate['deleted_at']}");
                }
                $this->line("    Created at: {$duplicate['created_at']}");
            }

            if ($fix) {
                $this->fixDuplicates($result['duplicates']);
            } else {
                $this->info("\n💡 Use --fix flag to automatically fix duplicates");
            }
        } else {
            $this->info("\n✅ No duplicates found!");
        }

        return 0;
    }

    /**
     * Fix duplicate job order numbers
     */
    private function fixDuplicates($duplicates)
    {
        $this->info("\n🔧 Fixing duplicates...");

        foreach ($duplicates as $duplicate) {
            if ($duplicate['is_deleted']) {
                $this->line("  Skipping deleted record ID {$duplicate['id']} (nomor: {$duplicate['nomor_job_order']})");
                continue;
            }

            $this->line("  Fixing active record ID {$duplicate['id']} (nomor: {$duplicate['nomor_job_order']})");

            // Generate nomor baru yang unik
            try {
                $service = new JobOrderNumberService();
                $newNomor = $service->generateSafeJobOrderNumber();

                // Update record dengan nomor baru
                $model = \App\Models\JobPrepress::find($duplicate['id']);
                if ($model) {
                    $oldNomor = $model->nomor_job_order;
                    $model->nomor_job_order = $newNomor;
                    $model->save();

                    $this->line("    ✅ Updated: {$oldNomor} → {$newNomor}");
                } else {
                    $this->error("    ❌ Record not found!");
                }
            } catch (\Exception $e) {
                $this->error("    ❌ Error: " . $e->getMessage());
            }
        }

        $this->info("\n🎯 Duplicate fixing completed!");
    }
}
