<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrainingMaster;
use App\Models\TrainingSession;
use App\Models\TrainingMaterial;
use App\Models\TrainingQuestionBank;
use App\Models\User;

class ListTrainingForAssignment extends Command
{
    protected $signature = 'training:list-for-assignment';
    protected $description = 'List all trainings with their materials and question counts for assignment';

    public function handle()
    {
        $this->info('=== DAFTAR TRAINING UNTUK ASSIGNMENT ===');
        $this->newLine();

        $trainings = TrainingMaster::with(['materials', 'sessions'])->get();

        if ($trainings->isEmpty()) {
            $this->error('Tidak ada training ditemukan!');
            return;
        }

        foreach ($trainings as $training) {
            $this->info("Training ID: {$training->id}");
            $this->line("  Nama: {$training->training_name}");
            $this->line("  Kode: {$training->training_code}");
            $this->line("  Status: {$training->status}");
            $this->line("  Active: " . ($training->is_active ? 'Yes' : 'No'));
            
            // Count materials
            $materials = $training->materials;
            $this->line("  Total Materials: " . $materials->count());
            
            if ($materials->count() > 0) {
                $this->line("  Materials:");
                foreach ($materials as $material) {
                    $questionCount = TrainingQuestionBank::where('material_id', $material->id)
                        ->where('is_active', true)
                        ->count();
                    $this->line("    - ID {$material->id}: {$material->material_title} ({$questionCount} questions)");
                }
            } else {
                $this->warn("    ⚠ Tidak ada materials!");
            }

            // Count sessions
            $sessions = $training->sessions;
            $this->line("  Total Sessions: " . $sessions->count());
            
            if ($sessions->count() > 0) {
                $this->line("  Sessions:");
                foreach ($sessions as $session) {
                    // Count available questions for this session
                    $availableQuestions = $this->countAvailableQuestions($session, $materials);
                    $this->line("    - ID {$session->id}: {$session->session_title} (Order: {$session->session_order})");
                    $this->line("      Difficulty Level ID: " . ($session->difficulty_level_id ?? 'NULL'));
                    $this->line("      Questions Needed: {$session->question_count}");
                    $this->line("      Available Questions: {$availableQuestions}");
                    
                    if ($availableQuestions < $session->question_count) {
                        $this->warn("      ⚠ Not enough questions!");
                    } else {
                        $this->info("      ✓ Sufficient questions");
                    }
                }
            } else {
                $this->warn("    ⚠ Tidak ada sessions!");
            }

            $this->newLine();
        }

        $this->info('=== REKOMENDASI ===');
        $this->line('Training yang sudah lengkap (punya materials + questions + sessions):');
        
        foreach ($trainings as $training) {
            $materials = $training->materials;
            $sessions = $training->sessions;
            
            if ($materials->count() > 0 && $sessions->count() > 0) {
                $allSessionsOk = true;
                foreach ($sessions as $session) {
                    $availableQuestions = $this->countAvailableQuestions($session, $materials);
                    if ($availableQuestions < $session->question_count) {
                        $allSessionsOk = false;
                        break;
                    }
                }
                
                if ($allSessionsOk) {
                    $this->info("  ✓ Training ID {$training->id}: {$training->training_name}");
                    $this->line("    Training Code: {$training->training_code}");
                    $this->line("    Materials: {$materials->count()}");
                    $this->line("    Sessions: {$sessions->count()}");
                }
            }
        }
    }

    private function countAvailableQuestions($session, $materials)
    {
        if ($materials->isEmpty()) {
            return 0;
        }

        $materialIds = $materials->pluck('id')->toArray();
        
        $query = TrainingQuestionBank::active()
            ->whereIn('material_id', $materialIds);
        
        if ($session->difficulty_level_id) {
            $query->where('difficulty_level_id', $session->difficulty_level_id);
        }
        
        if ($session->theme) {
            $query->where('theme', $session->theme);
        }
        
        return $query->count();
    }
}

