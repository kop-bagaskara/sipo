<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingAssignment;
use App\Models\TrainingSession;
use App\Models\TrainingMaster;
use App\Models\TrainingQuestionBank;

class CheckTrainingData extends Command
{
    protected $signature = 'training:check {assignment_id} {session_id}';
    protected $description = 'Check training data for assignment and session';

    public function handle()
    {
        $assignmentId = $this->argument('assignment_id');
        $sessionId = $this->argument('session_id');

        $this->info("=== CHECKING TRAINING DATA ===");
        $this->info("Assignment ID: {$assignmentId}");
        $this->info("Session ID: {$sessionId}");
        $this->newLine();

        // 1. Check Assignment
        $this->info("1. ASSIGNMENT DETAIL:");
        $assignment = TrainingAssignment::with('training')->find($assignmentId);
        if ($assignment) {
            $this->line("   ✓ Assignment ID: {$assignment->id}");
            $this->line("   ✓ Training ID: {$assignment->training_id}");
            $this->line("   ✓ Employee ID: {$assignment->employee_id}");
            $this->line("   ✓ Status: {$assignment->status}");
            if ($assignment->training) {
                $this->line("   ✓ Training Name: {$assignment->training->training_name}");
                $this->line("   ✓ Training Code: {$assignment->training->training_code}");
            } else {
                $this->error("   ✗ Training not found!");
            }
        } else {
            $this->error("   ✗ Assignment not found!");
            return;
        }
        $this->newLine();

        // 2. Check Session
        $this->info("2. SESSION DETAIL:");
        $session = TrainingSession::with('difficultyLevel')->find($sessionId);
        if ($session) {
            $this->line("   ✓ Session ID: {$session->id}");
            $this->line("   ✓ Session Title: {$session->session_title}");
            $this->line("   ✓ Session Order: {$session->session_order}");
            $this->line("   ✓ Training ID: {$session->training_id}");
            $this->line("   ✓ Difficulty Level ID: {$session->difficulty_level_id}");
            $this->line("   ✓ Theme: " . ($session->theme ?? 'NULL'));
            $this->line("   ✓ Question Count Needed: {$session->question_count}");
            $this->line("   ✓ Passing Score: {$session->passing_score}");
            $this->line("   ✓ Is Active: " . ($session->is_active ? 'Yes' : 'No'));
        } else {
            $this->error("   ✗ Session not found!");
            return;
        }
        $this->newLine();

        // 3. Check Training Master Materials
        $this->info("3. TRAINING MASTER MATERIALS:");
        $training = TrainingMaster::with('materials')->find($assignment->training_id);
        if ($training) {
            $materials = $training->materials;
            $this->line("   Total Materials: " . $materials->count());
            if ($materials->count() > 0) {
                foreach ($materials as $material) {
                    $this->line("   - Material ID: {$material->id} | {$material->material_title} | Active: " . ($material->is_active ? 'Yes' : 'No'));
                }
            } else {
                $this->error("   ✗ No materials found for this training!");
            }
        } else {
            $this->error("   ✗ Training Master not found!");
        }
        $this->newLine();

        // 4. Check Question Bank
        $this->info("4. QUESTION BANK AVAILABILITY:");
        if ($training && $materials->count() > 0) {
            $materialIds = $materials->pluck('id')->toArray();
            
            $query = TrainingQuestionBank::active()
                ->whereIn('material_id', $materialIds);
            
            if ($session->difficulty_level_id) {
                $query->where('difficulty_level_id', $session->difficulty_level_id);
            }
            
            if ($session->theme) {
                $query->where('theme', $session->theme);
            }
            
            $availableQuestions = $query->get();
            $totalAvailable = $availableQuestions->count();
            
            $this->line("   Total Questions Available: {$totalAvailable}");
            $this->line("   Questions Needed: {$session->question_count}");
            
            if ($totalAvailable >= $session->question_count) {
                $this->info("   ✓ Sufficient questions available!");
            } else {
                $this->error("   ✗ Not enough questions! Need {$session->question_count}, but only {$totalAvailable} available.");
            }
            
            if ($totalAvailable > 0) {
                $this->newLine();
                $this->line("   Breakdown by Material:");
                foreach ($materials as $material) {
                    $materialQuestions = $availableQuestions->where('material_id', $material->id);
                    $count = $materialQuestions->count();
                    $this->line("   - {$material->material_title}: {$count} questions");
                }
                
                $this->newLine();
                $this->line("   Sample Questions (first 5):");
                foreach ($availableQuestions->take(5) as $q) {
                    $this->line("   - Q{$q->id}: " . substr($q->question, 0, 50) . "...");
                }
            }
        } else {
            $this->error("   ✗ Cannot check questions - no materials found!");
        }
        $this->newLine();

        // 5. Check Session Progress
        $this->info("5. SESSION PROGRESS:");
        $progress = DB::connection('pgsql3')
            ->table('tb_training_session_progress')
            ->where('assignment_id', $assignmentId)
            ->where('session_id', $sessionId)
            ->first();
        
        if ($progress) {
            $this->line("   ✓ Progress exists");
            $this->line("   ✓ Status: {$progress->status}");
            $this->line("   ✓ Score: " . ($progress->score ?? 'N/A'));
            $this->line("   ✓ Total Questions: {$progress->total_questions}");
            
            $questionsData = json_decode($progress->questions_data, true);
            if ($questionsData) {
                $this->line("   ✓ Questions Generated: " . count($questionsData));
            } else {
                $this->error("   ✗ No questions data found in progress!");
            }
        } else {
            $this->line("   - No progress record found (session not started yet)");
        }
        $this->newLine();

        // 6. Summary
        $this->info("=== SUMMARY ===");
        $issues = [];
        
        if (!$training) {
            $issues[] = "Training Master not found";
        } elseif ($materials->count() == 0) {
            $issues[] = "No materials linked to training master";
        } elseif (isset($totalAvailable) && $totalAvailable < $session->question_count) {
            $issues[] = "Not enough questions in question bank";
        }
        
        if (empty($issues)) {
            $this->info("✓ All checks passed! System should work correctly.");
        } else {
            $this->error("✗ Issues found:");
            foreach ($issues as $issue) {
                $this->error("  - {$issue}");
            }
        }
    }
}

