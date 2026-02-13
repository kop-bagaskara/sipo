<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrainingMaster;
use App\Models\TrainingAssignment;
use App\Models\User;
use Carbon\Carbon;

class CreateTrainingAssignment extends Command
{
    protected $signature = 'training:create-assignment 
                            {training_id : ID dari training master}
                            {employee_id : ID dari employee/user}
                            {--start_date= : Tanggal mulai (format: Y-m-d, default: hari ini)}
                            {--deadline_date= : Tanggal deadline (format: Y-m-d)}';
    
    protected $description = 'Create new training assignment';

    public function handle()
    {
        $trainingId = $this->argument('training_id');
        $employeeId = $this->argument('employee_id');
        $startDate = $this->option('start_date') ? Carbon::parse($this->option('start_date')) : Carbon::today();
        $deadlineDate = $this->option('deadline_date') ? Carbon::parse($this->option('deadline_date')) : Carbon::today()->addDays(30);

        // Check training
        $training = TrainingMaster::find($trainingId);
        if (!$training) {
            $this->error("Training ID {$trainingId} tidak ditemukan!");
            return 1;
        }

        // Check employee
        $employee = User::find($employeeId);
        if (!$employee) {
            $this->error("Employee ID {$employeeId} tidak ditemukan!");
            return 1;
        }

        // Check if assignment already exists
        $existingAssignment = TrainingAssignment::where('training_id', $trainingId)
            ->where('employee_id', $employeeId)
            ->where('status', '!=', 'completed')
            ->first();

        if ($existingAssignment) {
            $this->warn("Assignment sudah ada untuk training ini!");
            $this->line("Assignment ID: {$existingAssignment->id}");
            $this->line("Status: {$existingAssignment->status}");
            
            if (!$this->confirm('Apakah Anda ingin membuat assignment baru?', false)) {
                return 0;
            }
        }

        // Get current user as assigned_by
        $assignedBy = User::first(); // Atau gunakan auth()->user() jika di web context

        // Create assignment
        $assignment = TrainingAssignment::create([
            'training_id' => $trainingId,
            'employee_id' => $employeeId,
            'status' => 'assigned',
            'assigned_date' => Carbon::today(),
            'start_date' => $startDate,
            'deadline_date' => $deadlineDate,
            'progress_percentage' => 0.00,
            'assigned_by' => $assignedBy->id ?? null,
        ]);

        $this->info("=== ASSIGNMENT BERHASIL DIBUAT ===");
        $this->line("Assignment ID: {$assignment->id}");
        $this->line("Training: {$training->training_name} (ID: {$training->id})");
        $this->line("Employee: {$employee->name} (ID: {$employee->id})");
        $this->line("Status: {$assignment->status}");
        $this->line("Start Date: {$startDate->format('Y-m-d')}");
        $this->line("Deadline: {$deadlineDate->format('Y-m-d')}");
        $this->newLine();
        $this->info("URL untuk mengakses: /hr/portal-training/sessions/{$assignment->id}/2");
        $this->info("Atau jalankan: php artisan training:check {$assignment->id} 2");

        return 0;
    }
}

