<?php

namespace App\Http\Controllers\PortalTraining;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingAssignment;
use App\Models\TrainingMaterial;
use App\Models\TrainingExam;
use App\Models\TrainingExamQuestion;
use App\Models\TrainingQuestionBank;
use App\Models\TrainingResult;
use App\Models\TrainingMaterialProgress;
use App\Models\TrainingSession;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Display exam page for a material
     *
     * @param int $materialId
     * @return \Illuminate\View\View
     */
    public function show($materialId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        $material = TrainingMaterial::findOrFail($materialId);

        // Cek apakah user sudah menyelesaikan materi (video progress 100%)
        $progress = TrainingMaterialProgress::where('material_id', $materialId)
            ->where('employee_id', $employeeId)
            ->where('status', 'completed')
            ->first();

        if (!$progress) {
            return redirect()->route('hr.portal-training.materials.show', $materialId)
                ->with('error', 'Harap selesaikan materi terlebih dahulu sebelum mengikuti ujian.');
        }

        // Cek apakah user sudah pernah ikut ujian untuk material ini
        $existingExam = TrainingExam::where('material_id', $materialId)
            ->where('employee_id', $employeeId)
            ->first();

        if ($existingExam && $existingExam->status == 'completed') {
            return redirect()->route('hr.portal-training.exams.result', $existingExam->id);
        }

        return view('portal-training.exams.show', compact('material'));
    }

    /**
     * Start exam - generate random questions
     *
     * @param Request $request
     * @param int $materialId
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Request $request, $materialId)
    {
        $request->validate([
            'assignment_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = DB::connection('pgsql3')
                        ->table('tb_training_assignments')
                        ->where('id', $value)
                        ->exists();
                    if (!$exists) {
                        $fail('Assignment tidak ditemukan.');
                    }
                },
            ],
        ]);

        $user = Auth::user();
        $employeeId = $user->id;

        $material = TrainingMaterial::findOrFail($materialId);

        // Cek apakah user sudah menyelesaikan materi
        $progress = TrainingMaterialProgress::where('material_id', $materialId)
            ->where('employee_id', $employeeId)
            ->where('status', 'completed')
            ->first();

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Harap selesaikan materi terlebih dahulu.'
            ], 403);
        }

        // Cek apakah sudah ada exam yang sedang berjalan
        $existingExam = TrainingExam::where('material_id', $materialId)
            ->where('employee_id', $employeeId)
            ->where('status', 'in_progress')
            ->first();

        if ($existingExam) {
            return response()->json([
                'success' => true,
                'exam_id' => $existingExam->id,
                'message' => 'Melanjutkan ujian yang sedang berjalan.'
            ]);
        }

        // Ambil pertanyaan random dari bank soal berdasarkan kategori materi
        // Distribusi soal berdasarkan difficulty level:
        // - 20% very easy (paling mudah)
        // - 30% easy (mudah)
        // - 30% medium (cukup)
        // - 15% hard (menengah ke atas)
        // - 5% very hard (sulit)

        $veryEasyQuestions = TrainingQuestionBank::where('category_id', $material->category_id)
            ->where('difficulty_level', 'paling mudah')
            ->inRandomOrder()
            ->limit(2) // 20% dari 10 soal
            ->get();

        $easyQuestions = TrainingQuestionBank::where('category_id', $material->category_id)
            ->where('difficulty_level', 'mudah')
            ->inRandomOrder()
            ->limit(3) // 30% dari 10 soal
            ->get();

        $mediumQuestions = TrainingQuestionBank::where('category_id', $material->category_id)
            ->where('difficulty_level', 'cukup')
            ->inRandomOrder()
            ->limit(3) // 30% dari 10 soal
            ->get();

        $hardQuestions = TrainingQuestionBank::where('category_id', $material->category_id)
            ->where('difficulty_level', 'menengah ke atas')
            ->inRandomOrder()
            ->limit(1) // 15% dari 10 soal, dibulatkan
            ->get();

        $veryHardQuestions = TrainingQuestionBank::where('category_id', $material->category_id)
            ->where('difficulty_level', 'sulit')
            ->inRandomOrder()
            ->limit(1) // 5% dari 10 soal, dibulatkan
            ->get();

        // Gabungkan semua soal
        $allQuestions = collect()
            ->concat($veryEasyQuestions)
            ->concat($easyQuestions)
            ->concat($mediumQuestions)
            ->concat($hardQuestions)
            ->concat($veryHardQuestions);

        // Jika soal tidak cukup, ambil dari medium sebagai fallback
        if ($allQuestions->count() < 10) {
            $additionalQuestions = TrainingQuestionBank::where('category_id', $material->category_id)
                ->whereNotIn('id', $allQuestions->pluck('id'))
                ->inRandomOrder()
                ->limit(10 - $allQuestions->count())
                ->get();
            $allQuestions = $allQuestions->concat($additionalQuestions);
        }

        // Shuffle urutan soal agar setiap user dapat urutan berbeda
        $allQuestions = $allQuestions->shuffle();

        // Buat exam record
        $exam = TrainingExam::create([
            'assignment_id' => $request->assignment_id,
            'material_id' => $materialId,
            'employee_id' => $employeeId,
            'questions' => $allQuestions->pluck('id')->toArray(), // Simpan ID soal sebagai JSON
            'status' => 'in_progress',
            'start_time' => Carbon::now(),
        ]);

        // Buat exam question records dengan urutan random
        $order = 1;
        foreach ($allQuestions as $question) {
            TrainingExamQuestion::create([
                'exam_id' => $exam->id,
                'question_bank_id' => $question->id,
                'question_order' => $order++,
            ]);
        }

        return response()->json([
            'success' => true,
            'exam_id' => $exam->id,
            'questions' => $allQuestions->map(function($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'type' => $q->type,
                    'difficulty' => $q->difficulty_level,
                    'options' => $q->options ? json_decode($q->options) : null,
                    'points' => $q->points,
                ];
            }),
            'message' => 'Ujian dimulai. Semoga berhasil!'
        ]);
    }

    /**
     * Submit answer for a question
     *
     * @param Request $request
     * @param int $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitAnswer(Request $request, $examId)
    {
        $request->validate([
            'question_id' => 'required|exists:training_question_banks,id',
            'answer' => 'required',
        ]);

        $user = Auth::user();
        $employeeId = $user->id;

        $exam = TrainingExam::where('id', $examId)
            ->where('employee_id', $employeeId)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $question = TrainingQuestionBank::findOrFail($request->question_id);

        // Cek jawaban benar
        $isCorrect = false;
        if ($question->type == 'pilihan ganda') {
            $isCorrect = $request->answer == $question->correct_answer;
        } elseif ($question->type == 'essay') {
            // Essay perlu manual grading, sementara dianggap belum dinilai
            $isCorrect = null;
        }

        // Update atau buat exam question record
        $examQuestion = TrainingExamQuestion::where('exam_id', $examId)
            ->where('question_bank_id', $request->question_id)
            ->first();

        if ($examQuestion) {
            $examQuestion->update([
                'user_answer' => $request->answer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect === true ? $question->points : 0,
            ]);
        }

        // Update answers JSON di exam
        $answers = $exam->answers ? json_decode($exam->answers, true) : [];
        $answers[$request->question_id] = $request->answer;
        $exam->update(['answers' => json_encode($answers)]);

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
            'message' => 'Jawaban berhasil disimpan.'
        ]);
    }

    /**
     * Finish exam - calculate score
     *
     * @param Request $request
     * @param int $examId
     * @return \Illuminate\Http\JsonResponse
     */
    public function finish(Request $request, $examId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        $exam = TrainingExam::where('id', $examId)
            ->where('employee_id', $employeeId)
            ->where('status', 'in_progress')
            ->firstOrFail();

        // Hitung total score
        $examQuestions = TrainingExamQuestion::where('exam_id', $examId)->get();
        $totalScore = $examQuestions->sum('points_earned');
        $maxScore = $examQuestions->sum(function($eq) {
            return TrainingQuestionBank::find($eq->question_bank_id)->points;
        });

        // Update exam
        $exam->update([
            'score' => $totalScore,
            'status' => 'completed',
            'end_time' => Carbon::now(),
            'duration' => Carbon::parse($exam->start_time)->diffInSeconds(Carbon::now()),
        ]);

        // Tentukan passing score (misal 70% dari max score)
        $passingScore = $maxScore * 0.7;
        $passed = $totalScore >= $passingScore;

        // Buat result record
        $result = TrainingResult::create([
            'assignment_id' => $exam->assignment_id,
            'employee_id' => $employeeId,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'passing_score' => $passingScore,
            'status' => $passed ? 'passed' : 'failed',
            'completed_date' => Carbon::now(),
        ]);

        // Generate sertifikat jika lulus
        if ($passed) {
            $certificatePath = $this->generateCertificate($result->id);
            $result->update(['certificate_path' => $certificatePath]);
        }

        // Cari next session setelah selesai ujian
        $assignment = TrainingAssignment::find($exam->assignment_id);
        $nextSession = null;
        if ($assignment && $assignment->training) {
            // Ambil session pertama dari training (atau bisa juga ambil berdasarkan material)
            $nextSession = TrainingSession::where('training_id', $assignment->training_id)
                ->active()
                ->orderBy('session_order', 'asc')
                ->first();
        }

        return response()->json([
            'success' => true,
            'score' => $totalScore,
            'max_score' => $maxScore,
            'passing_score' => $passingScore,
            'passed' => $passed,
            'result_id' => $result->id,
            'message' => $passed
                ? 'Selamat! Anda lulus ujian.'
                : 'Maaf, Anda belum lulus. Silakan coba lagi.',
            'next_session_url' => $nextSession && $assignment
                ? route('hr.portal-training.sessions.show', [$assignment->id, $nextSession->id])
                : route('hr.portal-training.index')
        ]);
    }

    /**
     * Display exam result
     *
     * @param int $examId
     * @return \Illuminate\View\View
     */
    public function result($examId)
    {
        $user = Auth::user();
        $employeeId = $user->id;

        $exam = TrainingExam::with(['material', 'assignment.training'])
            ->where('id', $examId)
            ->where('employee_id', $employeeId)
            ->firstOrFail();

        $result = TrainingResult::where('assignment_id', $exam->assignment_id)
            ->where('employee_id', $employeeId)
            ->first();

        // Ambil detail jawaban
        $examQuestions = TrainingExamQuestion::with('questionBank')
            ->where('exam_id', $examId)
            ->orderBy('question_order')
            ->get();

        return view('portal-training.exams.result', compact(
            'exam',
            'result',
            'examQuestions'
        ));
    }

    /**
     * Generate certificate for passed exam
     *
     * @param int $resultId
     * @return string
     */
    private function generateCertificate($resultId)
    {
        // TODO: Implement PDF certificate generation
        // Untuk sementara return path dummy
        $result = TrainingResult::find($resultId);
        $certificatePath = 'certificates/training_' . $result->id . '_' . time() . '.pdf';

        // Di sini nanti bisa menggunakan library seperti:
        // - dompdf
        // - snappy
        // - tcpdf
        // Atau generate image dengan GD/ImageMagick lalu convert ke PDF

        return $certificatePath;
    }
}
