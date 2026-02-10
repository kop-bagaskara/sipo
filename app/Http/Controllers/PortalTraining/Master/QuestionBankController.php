<?php

namespace App\Http\Controllers\PortalTraining\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingQuestionBank;
use App\Models\TrainingMaterialCategory;
use App\Models\TrainingDifficultyLevel;
use App\Models\TrainingMaterial;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of questions
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = TrainingMaterialCategory::orderBy('display_order', 'asc')->get();
        $difficultyLevels = TrainingDifficultyLevel::orderBy('display_order', 'asc')->get();
        return view('portal-training.master.question-banks.index', compact('categories', 'difficultyLevels'));
    }

    /**
     * Get data for DataTable
     *
     * @return \Yajra\DataTables\DataTables
     */
    public function getData()
    {
        $questions = TrainingQuestionBank::with(['material', 'difficultyLevel'])
            ->orderBy('id', 'desc');

        return DataTables::of($questions)
            ->addIndexColumn()
            ->addColumn('category_name', function($question) {
                return $question->material ? $question->material->material_title : '-';
            })
            ->addColumn('difficulty_badge', function($question) {
                if (!$question->difficultyLevel) {
                    return '<span class="badge badge-secondary">-</span>';
                }
                $levelName = $question->difficultyLevel->level_name;
                $colors = [
                    'paling mudah' => 'success',
                    'mudah' => 'info',
                    'cukup' => 'warning',
                    'menengah ke atas' => 'orange',
                    'sulit' => 'danger',
                ];
                $color = $colors[$levelName] ?? 'secondary';
                return '<span class="badge badge-'.$color.'">'.$levelName.'</span>';
            })
            ->addColumn('type_badge', function($question) {
                $types = [
                    'multiple_choice' => 'Pilihan Ganda',
                    'essay' => 'Essay',
                    'true_false' => 'Benar/Salah',
                    'fill_blank' => 'Isian'
                ];
                $label = $types[$question->question_type] ?? ucfirst(str_replace('_', ' ', $question->question_type));
                return '<span class="badge badge-type">'.$label.'</span>';
            })
            ->addColumn('question_preview', function($question) {
                return strlen($question->question) > 100
                    ? substr($question->question, 0, 100) . '...'
                    : $question->question;
            })
            ->addColumn('points', function($question) {
                return $question->score;
            })
            ->addColumn('status_badge', function($question) {
                if ($question->is_active) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-secondary">Tidak Aktif</span>';
                }
            })
            ->addColumn('action', function($question) {
                return '
                    <div class="action-buttons text-center">
                        <button type="button" class="btn btn-sm btn-primary btn-view" data-id="'.$question->id.'" title="Lihat">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info btn-edit" data-id="'.$question->id.'" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="'.$question->id.'" title="Hapus">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['difficulty_badge', 'type_badge', 'status_badge', 'action'])
            ->make(true);
    }

    /**
     * Store a new question
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:pilihan ganda,essay',
            'category_id' => 'required|integer',
            'difficulty_level' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'explanation' => 'nullable|string',
        ]);

        // Map form fields to database fields
        $typeMap = [
            'pilihan ganda' => 'multiple_choice',
            'essay' => 'essay'
        ];

        $question = TrainingQuestionBank::create([
            'question' => $request->question,
            'question_type' => $typeMap[$request->type],
            'material_id' => $request->category_id,
            'difficulty_level_id' => $this->getDifficultyLevelId($request->difficulty_level),
            'correct_answer' => $request->correct_answer,
            'answer_options' => $request->type === 'pilihan ganda' ? json_encode($request->options ?? []) : null,
            'explanation' => $request->explanation,
            'score' => $request->points,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil ditambahkan ke bank soal.',
            'data' => $question
        ]);
    }

    /**
     * Show single question
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $question = TrainingQuestionBank::with(['material', 'difficultyLevel'])->findOrFail($id);

        $typeMap = [
            'multiple_choice' => 'pilihan ganda',
            'essay' => 'essay'
        ];

        return response()->json([
            'id' => $question->id,
            'question' => $question->question,
            'type' => $typeMap[$question->question_type] ?? $question->question_type,
            'category_id' => $question->material_id,
            'category_name' => $question->material ? $question->material->material_title : '-',
            'difficulty_level' => $question->difficultyLevel ? $question->difficultyLevel->level_name : '-',
            'correct_answer' => $question->correct_answer,
            'options' => $question->answer_options,
            'explanation' => $question->explanation,
            'points' => $question->score,
            'is_active' => $question->is_active,
        ]);
    }

    /**
     * Show the form for editing question
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $question = TrainingQuestionBank::with(['material', 'difficultyLevel'])->findOrFail($id);

        $typeMap = [
            'multiple_choice' => 'pilihan ganda',
            'essay' => 'essay'
        ];

        return response()->json([
            'id' => $question->id,
            'question' => $question->question,
            'type' => $typeMap[$question->question_type] ?? $question->question_type,
            'category_id' => $question->material_id,
            'difficulty_level' => $question->difficultyLevel ? $question->difficultyLevel->level_name : '',
            'correct_answer' => $question->correct_answer,
            'options' => $question->answer_options,
            'explanation' => $question->explanation,
            'points' => $question->score,
            'is_active' => $question->is_active,
        ]);
    }

    /**
     * Update question
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:pilihan ganda,essay',
            'category_id' => 'required|integer',
            'difficulty_level' => 'required|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'explanation' => 'nullable|string',
        ]);

        // Map form fields to database fields
        $typeMap = [
            'pilihan ganda' => 'multiple_choice',
            'essay' => 'essay'
        ];

        $question = TrainingQuestionBank::findOrFail($id);
        $question->update([
            'question' => $request->question,
            'question_type' => $typeMap[$request->type],
            'material_id' => $request->category_id,
            'difficulty_level_id' => $this->getDifficultyLevelId($request->difficulty_level),
            'correct_answer' => $request->correct_answer,
            'answer_options' => $request->type === 'pilihan ganda' ? json_encode($request->options ?? []) : null,
            'explanation' => $request->explanation,
            'score' => $request->points,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diupdate.',
            'data' => $question
        ]);
    }

    /**
     * Delete question
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $question = TrainingQuestionBank::findOrFail($id);

        // Cek apakah soal digunakan di exam
        if ($question->examQuestions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak dapat dihapus karena sudah digunakan dalam ujian.'
            ], 400);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil dihapus.'
        ]);
    }

    /**
     * Import questions from CSV file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120', // Max 5MB
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload.'
            ], 400);
        }

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        // Open and read CSV file
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka file CSV.'
            ], 400);
        }

        // Read header
        $header = fgetcsv($handle, 0, ',');
        if ($header === false) {
            fclose($handle);
            return response()->json([
                'success' => false,
                'message' => 'File CSV kosong atau format tidak valid.'
            ], 400);
        }

        // Validate required columns
        $requiredColumns = ['question', 'type', 'category_id', 'difficulty_level', 'correct_answer', 'points'];

        // Normalize header to lowercase and trim
        $headerNormalized = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

        // Check if all required columns exist (case-insensitive)
        $missingColumns = [];
        foreach ($requiredColumns as $required) {
            if (!in_array(strtolower($required), $headerNormalized)) {
                $missingColumns[] = $required;
            }
        }

        if (!empty($missingColumns)) {
            fclose($handle);
            return response()->json([
                'success' => false,
                'message' => 'Kolom wajib tidak lengkap: ' . implode(', ', $missingColumns) .
                             '. Kolom yang ditemukan: ' . implode(', ', $header)
            ], 400);
        }

        // Create mapping from normalized header to original header
        $headerMap = [];
        foreach ($header as $col) {
            $headerMap[strtolower(trim($col))] = $col;
        }

        // Map typeMap for database
        $typeMap = [
            'pilihan ganda' => 'multiple_choice',
            'essay' => 'essay'
        ];

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $rowNumber = 1; // Header is row 0, data starts from row 1

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Combine header with row data (case-insensitive access)
                $data = [];
                foreach ($header as $index => $colName) {
                    $normalizedKey = strtolower(trim($colName));
                    // Use coalesce to handle missing columns - pad empty strings if row is shorter
                    $value = isset($row[$index]) ? $row[$index] : '';
                    $data[$normalizedKey] = trim($value);
                }

                // Validate required fields
                if (empty($data['question']) || empty($data['type']) || empty($data['category_id']) ||
                    empty($data['difficulty_level']) || empty($data['correct_answer']) || empty($data['points'])) {

                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Data tidak lengkap";
                    continue;
                }

                // Validate type
                $type = strtolower($data['type']);
                if (!isset($typeMap[$type])) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Tipe soal tidak valid ({$data['type']})";
                    continue;
                }

                // Validate points
                if (!is_numeric($data['points']) || $data['points'] < 1 || $data['points'] > 100) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Poin harus antara 1-100";
                    continue;
                }

                // Validate category_id exists
                $category = TrainingMaterial::find($data['category_id']);
                if (!$category) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Kategori materi tidak valid (ID: {$data['category_id']})";
                    continue;
                }

                // Get difficulty level ID
                $difficultyLevelId = $this->getDifficultyLevelId($data['difficulty_level']);
                if (!$difficultyLevelId) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Tingkat kesulitan tidak valid ({$data['difficulty_level']})";
                    continue;
                }

                // For multiple choice, validate options
                $questionType = $typeMap[$type];
                $answerOptions = null;

                if ($questionType === 'multiple_choice') {
                    $options = [];
                    // Build options from CSV columns
                    $optionKeys = ['option_a', 'option_b', 'option_c', 'option_d'];
                    foreach ($optionKeys as $optionKey) {
                        if (isset($data[$optionKey]) && !empty(trim($data[$optionKey]))) {
                            $options[] = trim($data[$optionKey]);
                        }
                    }

                    if (count($options) < 2) {
                        $errorCount++;
                        $errors[] = "Baris $rowNumber: Pilihan ganda minimal harus memiliki 2 opsi (option_a, option_b, option_c, option_d). Ditemukan: " . count($options) . " opsi";
                        continue;
                    }

                    $answerOptions = json_encode($options);
                }

                // Create question
                try {
                    TrainingQuestionBank::create([
                        'question' => $data['question'],
                        'question_type' => $questionType,
                        'material_id' => $data['category_id'],
                        'difficulty_level_id' => $difficultyLevelId,
                        'correct_answer' => $data['correct_answer'],
                        'answer_options' => $answerOptions,
                        'explanation' => $data['explanation'] ?? null,
                        'score' => $data['points'],
                        'is_active' => true,
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: " . $e->getMessage();
                }
            }

            fclose($handle);

            if ($successCount > 0) {
                DB::commit();

                $message = "Berhasil mengimport {$successCount} soal.";
                if ($errorCount > 0) {
                    $message .= " Gagal mengimport {$errorCount} soal.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'errors' => array_slice($errors, 0, 10) // Max 10 errors to show
                ]);
            } else {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada soal yang berhasil diimport.',
                    'errors' => array_slice($errors, 0, 10)
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if ($handle) fclose($handle);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Helper method to get difficulty level ID from name
     */
    private function getDifficultyLevelId($levelName)
    {
        $level = TrainingDifficultyLevel::where('level_name', $levelName)->first();
        return $level ? $level->id : null;
    }
}
