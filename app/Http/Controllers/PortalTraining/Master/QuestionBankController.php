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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionBankImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
        $materials = TrainingMaterial::orderBy('display_order', 'asc')->get(); // Tambahkan materials untuk dropdown
        return view('portal-training.master.question-banks.index', compact('categories', 'difficultyLevels', 'materials'));
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
            ->addColumn('theme', function($question) {
                return $question->theme ?: '-';
            })
            ->addColumn('type_number', function($question) {
                return $question->type_number ? 'TIPE ' . $question->type_number : '-';
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
            ->rawColumns(['difficulty_badge', 'type_badge', 'status_badge', 'action', 'theme', 'type_number'])
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
            'theme' => 'nullable|string|max:255',
            'type_number' => 'nullable|integer|min:1|max:10',
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
            'theme' => $request->theme,
            'type_number' => $request->type_number,
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
            'theme' => $question->theme,
            'type_number' => $question->type_number,
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
            'theme' => $question->theme,
            'type_number' => $question->type_number,
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
            'theme' => 'nullable|string|max:255',
            'type_number' => 'nullable|integer|min:1|max:10',
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
            'theme' => $request->theme,
            'type_number' => $request->type_number,
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
     * Import questions from Excel/CSV file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:10240', // Max 10MB, support Excel
        ]);

        // Validasi material_id dan difficulty_level_id dengan connection yang benar (hanya jika diisi)
        $materialId = $request->input('material_id');
        $difficultyLevelId = $request->input('difficulty_level_id');

        if (!empty($materialId) && $materialId != '0' && $materialId != '') {
            try {
                $material = TrainingMaterial::on('pgsql3')->find($materialId);
                if (!$material) {
                    // Cek apakah ada data di tabel untuk memberikan info yang lebih jelas
                    $totalMaterials = TrainingMaterial::on('pgsql3')->count();
                    $availableIds = TrainingMaterial::on('pgsql3')->pluck('id')->toArray();
                    
                    $message = 'Material ID tidak valid: ' . $materialId . '. ';
                    $message .= 'Sistem mencari di tabel: tb_training_materials (connection: pgsql3). ';
                    $message .= 'Total data di tabel: ' . $totalMaterials . '. ';
                    
                    if (!empty($availableIds)) {
                        $message .= 'ID yang tersedia: ' . implode(', ', array_slice($availableIds, 0, 10));
                        if (count($availableIds) > 10) {
                            $message .= '... (total ' . count($availableIds) . ' data)';
                        }
                    } else {
                        $message .= 'Tidak ada data di tabel. Silakan buat data Material terlebih dahulu di menu "Materi Training".';
                    }
                    
                    $message .= ' Atau kosongkan field Material ID untuk mengisi di file Excel.';
                    
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saat mencari Material ID: ' . $e->getMessage() . '. Pastikan tabel tb_training_materials ada di database pgsql3 atau kosongkan field ini untuk mengisi di file Excel.'
                ], 400);
            }
        }

        if (!empty($difficultyLevelId) && $difficultyLevelId != '0' && $difficultyLevelId != '') {
            $level = TrainingDifficultyLevel::on('pgsql3')->find($difficultyLevelId);
            if (!$level) {
                return response()->json([
                    'success' => false,
                    'message' => 'Difficulty Level ID tidak valid: ' . $difficultyLevelId . '. Pastikan Difficulty Level ID ada di database atau kosongkan field ini untuk mengisi di file Excel.'
                ], 400);
            }
        }

        // dd($request->all());

        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload.'
            ], 400);
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            // Use Excel import for xlsx, xls files
            if (in_array($extension, ['xlsx', 'xls'])) {
                $import = new QuestionBankImport(
                    $materialId,
                    $difficultyLevelId
                );

                Excel::import($import, $file);

                $successCount = $import->getSuccessCount();
                $errorCount = $import->getErrorCount();
                $errors = $import->getErrors();

                if ($successCount > 0) {
                    $message = "Berhasil mengimport {$successCount} soal.";
                    if ($errorCount > 0) {
                        $message .= " Gagal mengimport {$errorCount} soal.";
                    }

                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'success_count' => $successCount,
                        'error_count' => $errorCount,
                        'errors' => array_slice($errors, 0, 20) // Max 20 errors to show
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada soal yang berhasil diimport.',
                        'errors' => array_slice($errors, 0, 20)
                    ], 400);
                }
            } else {
                // Fallback to CSV import (existing logic)
                return $this->importCSV($file, $request);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Import CSV file (legacy method)
     */
    protected function importCSV($file, $request)
    {
        $filePath = $file->getRealPath();
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
        $headerNormalized = array_map(function($col) {
            return strtolower(trim($col));
        }, $header);

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

        $typeMap = [
            'pilihan ganda' => 'multiple_choice',
            'essay' => 'essay'
        ];

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $rowNumber++;

                if (empty(array_filter($row))) {
                    continue;
                }

                $data = [];
                foreach ($header as $index => $colName) {
                    $normalizedKey = strtolower(trim($colName));
                    $value = isset($row[$index]) ? $row[$index] : '';
                    $data[$normalizedKey] = trim($value);
                }

                if (empty($data['question']) || empty($data['type']) || empty($data['category_id']) ||
                    empty($data['difficulty_level']) || empty($data['correct_answer']) || empty($data['points'])) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Data tidak lengkap";
                    continue;
                }

                $type = strtolower($data['type']);
                if (!isset($typeMap[$type])) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Tipe soal tidak valid ({$data['type']})";
                    continue;
                }

                if (!is_numeric($data['points']) || $data['points'] < 1 || $data['points'] > 100) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Poin harus antara 1-100";
                    continue;
                }

                $category = TrainingMaterial::find($data['category_id']);
                if (!$category) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Kategori materi tidak valid (ID: {$data['category_id']})";
                    continue;
                }

                $difficultyLevelId = $this->getDifficultyLevelId($data['difficulty_level']);
                if (!$difficultyLevelId) {
                    $errorCount++;
                    $errors[] = "Baris $rowNumber: Tingkat kesulitan tidak valid ({$data['difficulty_level']})";
                    continue;
                }

                $questionType = $typeMap[$type];
                $answerOptions = null;

                if ($questionType === 'multiple_choice') {
                    $options = [];
                    $optionKeys = ['option_a', 'option_b', 'option_c', 'option_d'];
                    foreach ($optionKeys as $optionKey) {
                        if (isset($data[$optionKey]) && !empty(trim($data[$optionKey]))) {
                            $options[] = trim($data[$optionKey]);
                        }
                    }

                    if (count($options) < 2) {
                        $errorCount++;
                        $errors[] = "Baris $rowNumber: Pilihan ganda minimal harus memiliki 2 opsi";
                        continue;
                    }

                    $answerOptions = json_encode($options);
                }

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
                    'errors' => array_slice($errors, 0, 10)
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
     * Download Excel template for question bank import
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        // ========== SHEET 1: Format Baru (Direkomendasikan) ==========
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Format Baru');

        // Set header
        $headers = [
            'NO',
            'TEMA',
            'TIPE',
            'PERTANYAAN',
            'PILIHAN_A',
            'PILIHAN_B',
            'PILIHAN_C',
            'PILIHAN_D',
            'JAWABAN_BENAR',
            'MATERIAL_ID',
            'DIFFICULTY_LEVEL',
            'POINTS'
        ];

        // Style for header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // Set headers
        foreach ($headers as $colIndex => $header) {
            $cell = $sheet1->getCellByColumnAndRow($colIndex + 1, 1);
            $cell->setValue($header);
            $sheet1->getStyle($cell->getCoordinate())->applyFromArray($headerStyle);
        }

        // Set column widths
        $sheet1->getColumnDimension('A')->setWidth(5);  // NO
        $sheet1->getColumnDimension('B')->setWidth(20); // TEMA
        $sheet1->getColumnDimension('C')->setWidth(10); // TIPE
        $sheet1->getColumnDimension('D')->setWidth(50); // PERTANYAAN
        $sheet1->getColumnDimension('E')->setWidth(30); // PILIHAN_A
        $sheet1->getColumnDimension('F')->setWidth(30); // PILIHAN_B
        $sheet1->getColumnDimension('G')->setWidth(30); // PILIHAN_C
        $sheet1->getColumnDimension('H')->setWidth(30); // PILIHAN_D
        $sheet1->getColumnDimension('I')->setWidth(15); // JAWABAN_BENAR
        $sheet1->getColumnDimension('J')->setWidth(15); // MATERIAL_ID
        $sheet1->getColumnDimension('K')->setWidth(20); // DIFFICULTY_LEVEL
        $sheet1->getColumnDimension('L')->setWidth(10); // POINTS

        // Add example data
        $examples = [
            [1, 'RCCA', 'TIPE 1', 'Kesesuaian adalah?', 'Memenuhi kriteria yang dipersyaratkan/diwajibkan', 'Memenuhi sebagian kriteria yang diwajibkan', 'Memenuhi kriteria yang dilarang dalam peraturan', '', 'A', '', '', 10],
            [2, 'Metode', 'TIPE 2', 'Metode analisis yang memvisualisasikan hubungan antara efek dengan berbagai kategori penyebabnya disebut?', 'Control Chart', 'Pareto Chart', 'Scatter Diagram', 'Ishikawa Diagram', 'D', '', '', 10],
        ];

        $rowIndex = 2;
        foreach ($examples as $example) {
            foreach ($example as $colIndex => $value) {
                $cell = $sheet1->getCellByColumnAndRow($colIndex + 1, $rowIndex);
                $cell->setValue($value);
                
                // Add borders
                $sheet1->getStyle($cell->getCoordinate())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }
            $rowIndex++;
        }

        // Freeze first row
        $sheet1->freezePane('A2');

        // ========== SHEET 2: Format Lama (Multiple TIPE per baris) ==========
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Format Lama (Multiple TIPE)');

        // Set header untuk format lama
        $headersOld = ['NO', 'TEMA', 'TIPE 1', 'TIPE 2', 'TIPE 3', 'TIPE', 'JAWABAN_TIPE1', 'JAWABAN_TIPE2', 'JAWABAN_TIPE3', 'JAWABAN_TIPE'];

        // Set headers
        foreach ($headersOld as $colIndex => $header) {
            $cell = $sheet2->getCellByColumnAndRow($colIndex + 1, 1);
            $cell->setValue($header);
            $sheet2->getStyle($cell->getCoordinate())->applyFromArray($headerStyle);
        }

        // Set column widths untuk format lama
        $sheet2->getColumnDimension('A')->setWidth(5);  // NO
        $sheet2->getColumnDimension('B')->setWidth(20); // TEMA
        $sheet2->getColumnDimension('C')->setWidth(60); // TIPE 1
        $sheet2->getColumnDimension('D')->setWidth(60); // TIPE 2
        $sheet2->getColumnDimension('E')->setWidth(60); // TIPE 3
        $sheet2->getColumnDimension('F')->setWidth(60); // TIPE
        $sheet2->getColumnDimension('G')->setWidth(15); // JAWABAN_TIPE1
        $sheet2->getColumnDimension('H')->setWidth(15); // JAWABAN_TIPE2
        $sheet2->getColumnDimension('I')->setWidth(15); // JAWABAN_TIPE3
        $sheet2->getColumnDimension('J')->setWidth(15); // JAWABAN_TIPE

        // Add example data untuk format lama
        // Setiap TIPE berisi soal lengkap dengan format: Pertanyaan\nA. Pilihan A\nB. Pilihan B\nC. Pilihan C\nD. Pilihan D
        // Jawaban benar bisa ditandai dengan * di akhir pilihan atau di kolom terpisah
        $examplesOld = [
            [
                1,
                'RCCA',
                "Kesesuaian adalah (Conformity is)\nA. Memenuhi kriteria yang dipersyaratkan/diwajibkan. (Meeting required/mandated criteria.) *\nB. Memenuhi sebagian kriteria yang diwajibkan (Meeting some mandated criteria.)\nC. Memenuhi kriteria yang dilarang dalam peraturan (Meeting criteria prohibited by regulations.)",
                "Ketidaksesuaian (Non-conformity) adalah\nA. Kondisi di mana sistem berjalan sempurna (A condition where the system runs perfectly.)\nB. Saran perbaikan dari auditor (Improvement suggestions from the auditor.)\nC. Tidak terpenuhinya persyaratan (Failure to meet requirements.) *\nD. Terpenuhinya persyaratan secara parsial (Partial fulfillment of requirements.)",
                "Persyaratan adalah (Requirement is)\nA. Kesalahan yang ditemukan saat audit (Errors found during an audit.)\nB. Daftar hadir rapat tinjauan manajemen (Management review meeting attendance list.)\nC. Keinginan pribadi auditor yang tidak tertulis (Unwritten personal desires of the auditor.)\nD. Kebutuhan atau harapan yang dinyatakan, umumnya tersirat atau wajib (Stated, generally implied, or mandatory needs or expectations.) *",
                "Bukti Objektif adalah (Objective Evidence is)\nA. Rencana audit tahun depan (Next year's audit plan.)\nB. Kabar burung (gosip) dari karyawan (Rumors (gossip) from employees.)\nC. Data yang mendukung keberadaan suatu aktivitas atau proses (Data supporting the existence of an activity or process.) *\nD. Asumsi atau perkiraan auditor (Assumptions or estimates of the auditor.)",
                'A', // JAWABAN_TIPE1 (alternatif: bisa juga di cell dengan tanda *)
                'C', // JAWABAN_TIPE2
                'D', // JAWABAN_TIPE3
                'C'  // JAWABAN_TIPE
            ],
            [
                2,
                'Metode',
                "Manakah diantara di bawah ini yang merupakan alat untuk menganalisa penyebab masalah dengan metode 6M Factor yang langsung dijabarkan keseluruhan?\nA. Fault Tree Analysis\nB. Fishbone/Ishikawa *\nC. Why Why analysis",
                "Metode analisis yang memvisualisasikan hubungan antara efek (masalah) dengan berbagai kategori penyebabnya (biasanya 6M) dalam bentuk yang menyerupai tulang ikan disebut\nA. Control Chart\nB. Pareto Chart\nC. Scatter Diagram\nD. Ishikawa Diagram *",
                "Apa perbedaan utama antara 'Fishbone Analysis' dengan '5 Why Analysis'?\nA. Fishbone digunakan untuk mencari solusi, sedangkan 5 Why untuk mencari masalah\nB. 5 Why menggunakan diagram visual, Fishbone tidak\nC. Fishbone menjabarkan faktor penyebab secara luas (kategori), sedangkan 5 Why menggali penyebab secara mendalam (linear) *\nD. Fishbone hanya untuk industri jasa, 5 Why untuk manufaktur",
                "",
                'B', // JAWABAN_TIPE1
                'D', // JAWABAN_TIPE2
                'C', // JAWABAN_TIPE3
                ''   // JAWABAN_TIPE (kosong karena TIPE kosong)
            ],
        ];

        $rowIndex = 2;
        foreach ($examplesOld as $example) {
            foreach ($example as $colIndex => $value) {
                $cell = $sheet2->getCellByColumnAndRow($colIndex + 1, $rowIndex);
                $cell->setValue($value);
                
                // Set wrap text untuk kolom TIPE
                if ($colIndex >= 2) {
                    $sheet2->getStyle($cell->getCoordinate())->getAlignment()->setWrapText(true);
                }
                
                // Add borders
                $sheet2->getStyle($cell->getCoordinate())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            }
            $rowIndex++;
        }

        // Set row height untuk format lama (agar text terlihat)
        $sheet2->getRowDimension(2)->setRowHeight(100);
        $sheet2->getRowDimension(3)->setRowHeight(100);

        // Freeze first row
        $sheet2->freezePane('A2');

        // Set active sheet kembali ke sheet 1
        $spreadsheet->setActiveSheetIndex(0);

        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        $fileName = 'template_import_bank_soal_' . date('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
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
