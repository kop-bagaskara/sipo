<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\MathQuestion;
use Illuminate\Http\Request;

class MathQuestionController extends Controller
{
    /**
     * Display a listing of math questions.
     */
    public function index(Request $request)
    {
        $query = MathQuestion::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%")
                  ->orWhere('question_number', 'like', "%{$search}%");
            });
        }

        // Filter by question type
        if ($request->filled('question_type')) {
            $query->where('question_type', $request->question_type);
        }

        // Filter by difficulty level
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Order by question_number
        $query->orderBy('question_number', 'asc');

        $questions = $query->paginate(30);

        // Statistics
        $totalQuestions = MathQuestion::count();
        $activeQuestions = MathQuestion::where('is_active', true)->count();
        $inactiveQuestions = MathQuestion::where('is_active', false)->count();

        // Question types
        $questionTypes = [
            'pattern' => 'Pola Bilangan',
            'calculation' => 'Perhitungan',
            'conversion' => 'Konversi',
            'word_problem' => 'Soal Cerita'
        ];

        return view('hr.math-questions.index', compact('questions', 'totalQuestions', 'activeQuestions', 'inactiveQuestions', 'questionTypes'));
    }
}
