<?php

namespace App\Imports;

use App\Models\TrainingQuestionBank;
use App\Models\TrainingMaterial;
use App\Models\TrainingDifficultyLevel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;

class QuestionBankImport implements ToCollection, SkipsEmptyRows, WithChunkReading
{
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $materialId;
    protected $difficultyLevelId;

    public function __construct($materialId = null, $difficultyLevelId = null)
    {
        $this->materialId = $materialId;
        $this->difficultyLevelId = $difficultyLevelId;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        DB::connection('pgsql3')->beginTransaction();
        try {
            foreach ($rows as $rowIndex => $row) {
                $actualRowNumber = $rowIndex + 2; // +2 karena row 1 adalah header, dan index mulai dari 0

                // Skip header row jika ada
                if ($rowIndex === 0 && $this->isHeaderRow($row)) {
                    continue;
                }

                // Skip empty rows
                if ($row->filter(function($cell) { return !empty(trim($cell)); })->isEmpty()) {
                    continue;
                }

                // Format Excel user: NO | TEMA | TIPE 1 | TIPE 2 | TIPE 3 | TIPE
                // Setiap TIPE berisi: Pertanyaan + Pilihan A, B, C, D (jawaban benar di-highlight merah, tapi kita pakai kolom terpisah)
                
                // Cek apakah format baru (dengan kolom terpisah) atau format lama (semua dalam 1 kolom)
                $no = $row[0] ?? null; // Kolom A: NO
                $tema = trim($row[1] ?? ''); // Kolom B: TEMA

                // Deteksi format: 
                // Format Baru: NO | TEMA | TIPE | PERTANYAAN | PILIHAN_A | PILIHAN_B | PILIHAN_C | PILIHAN_D | JAWABAN_BENAR | MATERIAL_ID | DIFFICULTY_LEVEL | POINTS
                // Format Lama: NO | TEMA | TIPE 1 | TIPE 2 | TIPE 3 | TIPE (setiap TIPE berisi soal lengkap dalam 1 cell)
                
                // Cek apakah kolom 2 (index 2) adalah "TIPE" atau berisi soal lengkap
                $col2 = trim($row[2] ?? '');
                $col3 = trim($row[3] ?? ''); // PERTANYAAN (format baru) atau TIPE 2 (format lama)
                $col4 = trim($row[4] ?? ''); // PILIHAN_A (format baru) atau TIPE 3 (format lama)
                
                // Deteksi format baru:
                // 1. Kolom 2 berisi "TIPE" (bisa "TIPE", "TIPE 1", "TIPE 2", dll) ATAU
                // 2. Kolom 3 ada dan tidak ada newline (pertanyaan di kolom terpisah) DAN kolom 4 ada (PILIHAN_A)
                // 3. Kolom 3 panjangnya wajar untuk pertanyaan (< 500 karakter) dan tidak ada newline
                $col2IsTipe = !empty($col2) && preg_match('/^TIPE\s*\d*$/i', $col2);
                $col3IsQuestion = !empty($col3) && strpos($col3, "\n") === false && strlen($col3) < 500;
                $col4IsOption = !empty($col4) && strpos($col4, "\n") === false;
                
                // Format baru jika: (kolom 2 adalah TIPE) ATAU (kolom 3 adalah pertanyaan DAN kolom 4 adalah pilihan)
                $isNewFormat = $col2IsTipe || ($col3IsQuestion && $col4IsOption);
                
                // Pastikan kolom 3 dan 4 ada untuk format baru
                if ($isNewFormat && !empty($col3) && !empty($col4)) {
                    // Format baru dengan kolom terpisah
                    $this->processNewFormat($row, $actualRowNumber, $no, $tema);
                } else {
                    // Format lama: NO | TEMA | TIPE 1 | TIPE 2 | TIPE 3 | TIPE
                    // Process setiap TIPE sebagai soal terpisah dengan tema yang sama
                    // Tapi skip jika benar-benar kosong
                    if (empty($col2) && empty($col3) && empty($col4)) {
                        continue; // Skip row yang benar-benar kosong
                    }
                    $this->processOldFormat($row, $actualRowNumber, $no, $tema);
                }
            }

            DB::connection('pgsql3')->commit();
        } catch (\Exception $e) {
            DB::connection('pgsql3')->rollBack();
            Log::error('Question Bank Import Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process format baru dengan kolom terpisah
     */
    protected function processNewFormat($row, $rowNumber, $no, $tema)
    {
        // dd($row);
        try {
            // Format: NO | TEMA | TIPE | PERTANYAAN | PILIHAN_A | PILIHAN_B | PILIHAN_C | PILIHAN_D | JAWABAN_BENAR | MATERIAL_ID | DIFFICULTY_LEVEL | POINTS
            $tipe = trim($row[2] ?? '');
            $pertanyaan = trim($row[3] ?? '');
            $pilihanA = trim($row[4] ?? '');
            $pilihanB = trim($row[5] ?? '');
            $pilihanC = trim($row[6] ?? '');
            $pilihanD = trim($row[7] ?? '');
            $jawabanBenar = strtoupper(trim($row[8] ?? ''));
            $materialId = $this->materialId ?? (int)($row[9] ?? 0);
            $difficultyLevel = trim($row[10] ?? '');
            $points = (int)($row[11] ?? 10);

            // Validasi
            if (empty($pertanyaan)) {
                $this->errorCount++;
                $this->errors[] = "Baris $rowNumber: Pertanyaan tidak boleh kosong";
                return;
            }

            if (empty($pilihanA) || empty($pilihanB)) {
                $this->errorCount++;
                $this->errors[] = "Baris $rowNumber: Minimal harus ada 2 pilihan jawaban (A dan B)";
                return;
            }

            if (!in_array($jawabanBenar, ['A', 'B', 'C', 'D'])) {
                $this->errorCount++;
                $this->errors[] = "Baris $rowNumber: Jawaban benar harus A, B, C, atau D";
                return;
            }

            // Validasi material_id - gunakan connection yang benar
            $material = TrainingMaterial::on('pgsql3')->find($materialId);
            if (!$material) {
                $this->errorCount++;
                $this->errors[] = "Baris $rowNumber: Material ID tidak valid: $materialId";
                return;
            }

            // Get difficulty level ID - gunakan connection yang benar
            $difficultyLevelId = $this->difficultyLevelId;
            if (!$difficultyLevelId && !empty($difficultyLevel)) {
                $level = TrainingDifficultyLevel::on('pgsql3')->where('level_name', $difficultyLevel)->first();
                if ($level) {
                    $difficultyLevelId = $level->id;
                } else {
                    $this->errorCount++;
                    $this->errors[] = "Baris $rowNumber: Tingkat kesulitan tidak valid: $difficultyLevel";
                    return;
                }
            }

            if (!$difficultyLevelId) {
                $this->errorCount++;
                $this->errors[] = "Baris $rowNumber: Tingkat kesulitan harus diisi";
                return;
            }

            // Build options array
            $options = [$pilihanA];
            if (!empty($pilihanB)) $options[] = $pilihanB;
            if (!empty($pilihanC)) $options[] = $pilihanC;
            if (!empty($pilihanD)) $options[] = $pilihanD;

            // Get correct answer text
            $correctAnswerIndex = ord($jawabanBenar) - ord('A');
            $correctAnswerText = $options[$correctAnswerIndex] ?? '';

            // Extract type number from TIPE (e.g., "TIPE 1" -> 1)
            $typeNumber = null;
            if (!empty($tipe)) {
                preg_match('/\d+/', $tipe, $matches);
                if (!empty($matches)) {
                    $typeNumber = (int)$matches[0];
                }
            }

            // Create question
            TrainingQuestionBank::create([
                'question' => $pertanyaan,
                'question_type' => 'multiple_choice',
                'material_id' => $materialId,
                'difficulty_level_id' => $difficultyLevelId,
                'theme' => !empty($tema) ? $tema : null,
                'type_number' => $typeNumber,
                'correct_answer' => $jawabanBenar,
                'answer_options' => json_encode($options),
                'score' => $points,
                'is_active' => true,
            ]);

            $this->successCount++;
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->errors[] = "Baris $rowNumber: " . $e->getMessage();
        }
    }

    /**
     * Process format lama (NO | TEMA | TIPE 1 | TIPE 2 | TIPE 3 | TIPE | JAWABAN_TIPE1 | JAWABAN_TIPE2 | ...)
     * Setiap TIPE berisi soal lengkap dalam 1 cell
     * Setiap TIPE akan di-create sebagai soal terpisah dengan TEMA yang sama
     * Jawaban benar bisa di kolom terpisah atau ditandai dalam cell dengan format khusus
     */
    protected function processOldFormat($row, $rowNumber, $no, $tema)
    {
        // dd($row);
        // Kolom C, D, E, F adalah TIPE 1, TIPE 2, TIPE 3, TIPE
        // Kolom setelahnya bisa berisi JAWABAN_TIPE1, JAWABAN_TIPE2, dst (opsional)
        $tipeColumns = [];
        $answerColumns = []; // Map untuk jawaban benar per TIPE
        
        // Deteksi kolom TIPE mulai dari kolom C (index 2)
        // Loop sampai maksimal kolom Z (index 25) atau sampai tidak ada data
        for ($colIndex = 2; $colIndex <= 25; $colIndex++) {
            $cellValue = trim($row[$colIndex] ?? '');
            
            // Skip jika kolom kosong
            if (empty($cellValue)) {
                continue;
            }
            
            // Cek apakah ini kolom jawaban benar (format: "JAWABAN_TIPE1" atau "JAWABAN1" atau "A", "B", "C", "D")
            $colHeader = strtoupper($cellValue);
            if (preg_match('/^JAWABAN[_\s]?TIPE?[_\s]?(\d+)$/i', $colHeader, $matches) || 
                in_array($colHeader, ['A', 'B', 'C', 'D']) && strlen($colHeader) == 1) {
                // Ini kolom jawaban benar
                $tipeNum = isset($matches[1]) ? (int)$matches[1] : null;
                if ($tipeNum) {
                    $answerColumns[$tipeNum] = $colIndex;
                }
                continue;
            }
            
            // Cek apakah ini kolom TIPE (berisi soal lengkap dengan newline atau panjang > 50 karakter)
            // Format TIPE biasanya berisi pertanyaan + pilihan jawaban
            // Format lama HARUS memiliki newline (pertanyaan dan pilihan terpisah baris)
            // Jangan anggap sebagai format lama jika hanya 1 baris panjang tanpa newline
            $hasNewline = strpos($cellValue, "\n") !== false;
            $isLongText = strlen($cellValue) > 50;
            
            // Format lama HARUS memiliki newline (karena pertanyaan dan pilihan dalam 1 cell dipisah newline)
            // Jika tidak ada newline, kemungkinan ini format baru yang salah deteksi
            if ($hasNewline || ($isLongText && preg_match('/[A-D][\.\)\-\s]+/i', $cellValue))) {
                // Extract nomor TIPE dari header atau dari posisi kolom
                // Jika kolom 2 = TIPE 1, kolom 3 = TIPE 2, dst
                $tipeNumber = $colIndex - 1; // Kolom C (index 2) = TIPE 1, kolom D (index 3) = TIPE 2
                
                $tipeColumns[] = [
                    'col' => $colIndex,
                    'tipe' => $tipeNumber,
                    'cell' => $cellValue
                ];
            }
        }

        // Jika tidak ada kolom TIPE yang ditemukan, skip
        if (empty($tipeColumns)) {
            $this->errorCount++;
            $this->errors[] = "Baris $rowNumber: Tidak ditemukan kolom TIPE yang valid";
            return;
        }

        // Process setiap TIPE sebagai soal terpisah
        foreach ($tipeColumns as $tipeCol) {
            $cellValue = $tipeCol['cell'];
            
            try {
                // Parse soal dari cell value
                // Format dalam cell: "Pertanyaan?\nA. Pilihan A\nB. Pilihan B\nC. Pilihan C\nD. Pilihan D"
                // Atau dengan tanda jawaban benar: "Pertanyaan?\nA. Pilihan A *\nB. Pilihan B\nC. Pilihan C\nD. Pilihan D"
                $parsed = $this->parseQuestionFromCell($cellValue, $rowNumber, $tipeCol['tipe'], $tipeCol['col']);
                
                if (!$parsed || isset($parsed['error'])) {
                    $this->errorCount++;
                    $errorMsg = isset($parsed['error']) 
                        ? $parsed['error'] 
                        : "Baris $rowNumber, TIPE {$tipeCol['tipe']} (Kolom " . chr(65 + $tipeCol['col']) . "): Format soal tidak valid";
                    $this->errors[] = $errorMsg;
                    continue;
                }

                // Cek apakah ada jawaban benar di kolom terpisah
                $correctAnswer = $parsed['correct_answer']; // Default dari parsing
                if (isset($answerColumns[$tipeCol['tipe']])) {
                    $answerColIndex = $answerColumns[$tipeCol['tipe']];
                    $answerValue = strtoupper(trim($row[$answerColIndex] ?? ''));
                    if (in_array($answerValue, ['A', 'B', 'C', 'D'])) {
                        $correctAnswer = $answerValue;
                    }
                }

                // Validasi material_id
                $materialId = $this->materialId;
                if (!$materialId) {
                    $this->errorCount++;
                    $this->errors[] = "Baris $rowNumber, TIPE {$tipeCol['tipe']}: Material ID harus diisi (gunakan format baru atau set di form)";
                    continue;
                }
                
                // Validasi material_id exists dengan connection yang benar
                $material = TrainingMaterial::on('pgsql3')->find($materialId);
                if (!$material) {
                    $this->errorCount++;
                    $this->errors[] = "Baris $rowNumber, TIPE {$tipeCol['tipe']}: Material ID tidak valid: $materialId";
                    continue;
                }

                // Get difficulty level ID - gunakan connection yang benar
                $difficultyLevelId = $this->difficultyLevelId;
                if (!$difficultyLevelId) {
                    // Try to get default or first level
                    $level = TrainingDifficultyLevel::on('pgsql3')->orderBy('display_order')->first();
                    if ($level) {
                        $difficultyLevelId = $level->id;
                    } else {
                        $this->errorCount++;
                        $this->errors[] = "Baris $rowNumber, TIPE {$tipeCol['tipe']}: Tingkat kesulitan tidak ditemukan";
                        continue;
                    }
                }

                // Create question - setiap TIPE menjadi soal terpisah dengan TEMA yang sama
                TrainingQuestionBank::create([
                    'question' => $parsed['question'],
                    'question_type' => 'multiple_choice',
                    'material_id' => $materialId,
                    'difficulty_level_id' => $difficultyLevelId,
                    'theme' => !empty($tema) ? $tema : null, // TEMA sama untuk semua TIPE dalam 1 baris
                    'type_number' => $tipeCol['tipe'], // Nomor TIPE berbeda untuk setiap soal
                    'correct_answer' => $correctAnswer, // Gunakan jawaban benar dari kolom terpisah atau dari parsing
                    'answer_options' => json_encode($parsed['options']),
                    'score' => 10, // Default score
                    'is_active' => true,
                ]);

                $this->successCount++;
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = "Baris $rowNumber, TIPE {$tipeCol['tipe']} (Kolom " . chr(65 + $tipeCol['col']) . "): " . $e->getMessage();
            }
        }
    }

    /**
     * Parse question from cell value
     * Format: "Pertanyaan?\nA. Pilihan A\nB. Pilihan B\nC. Pilihan C\nD. Pilihan D"
     * Atau dengan tanda jawaban benar: "Pertanyaan?\nA. Pilihan A *\nB. Pilihan B\nC. Pilihan C\nD. Pilihan D"
     */
    protected function parseQuestionFromCell($cellValue, $rowNumber = null, $tipeNumber = null, $colIndex = null)
    {
        // Normalize line breaks (handle both \n and \r\n, dan juga karakter khusus Excel)
        $cellValue = str_replace(["\r\n", "\r"], "\n", $cellValue);
        
        // Jika tidak ada newline, coba split berdasarkan pattern pilihan (A., B., C., D.)
        // Ini untuk handle case dimana semua dalam 1 baris: "Pertanyaan? A. Pilihan A B. Pilihan B"
        if (strpos($cellValue, "\n") === false) {
            // Coba split berdasarkan pattern "A.", "B.", "C.", "D." di awal kata
            // Pattern: "Pertanyaan? A. Pilihan A B. Pilihan B C. Pilihan C D. Pilihan D"
            if (preg_match('/^(.+?)\s+([A-D][\.\)\-\s]+.+)$/i', $cellValue, $mainMatch)) {
                $question = trim($mainMatch[1]);
                $optionsText = trim($mainMatch[2]);
                
                // Split options berdasarkan pattern "A.", "B.", "C.", "D." di awal
                $optionsParts = preg_split('/\s+([A-D])[\.\)\-\s]+/i', $optionsText, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                
                if (count($optionsParts) >= 2) {
                    // Reconstruct lines array
                    $lines = [$question];
                    for ($i = 0; $i < count($optionsParts); $i += 2) {
                        if (isset($optionsParts[$i]) && isset($optionsParts[$i + 1])) {
                            $optionKey = strtoupper(trim($optionsParts[$i]));
                            $optionText = trim($optionsParts[$i + 1]);
                            $lines[] = $optionKey . '. ' . $optionText;
                        }
                    }
                } else {
                    // Fallback: split by space jika panjang > 50
                    $lines = [trim($cellValue)];
                }
            } else {
                // Jika tidak match pattern, anggap sebagai 1 baris
                $lines = [trim($cellValue)];
            }
        } else {
            // Split by newline (format normal)
            $lines = explode("\n", $cellValue);
        }
        
        // Filter dan trim lines
        $lines = array_filter(array_map('trim', $lines), function($line) {
            return !empty($line);
        });
        
        // Reset array keys
        $lines = array_values($lines);
        
        if (count($lines) < 2) {
            $colName = $colIndex !== null ? chr(65 + $colIndex) : '';
            $cellPreview = mb_substr($cellValue, 0, 100);
            if (mb_strlen($cellValue) > 100) {
                $cellPreview .= '...';
            }
            return [
                'error' => "Baris " . ($rowNumber ?? '?') . ($tipeNumber ? ", TIPE $tipeNumber" : '') . ($colName ? " (Kolom $colName)" : '') . ": Format tidak valid - minimal harus ada pertanyaan + 1 pilihan jawaban. Ditemukan: " . count($lines) . " baris. Isi cell: \"$cellPreview\". Pastikan format: Pertanyaan?\\nA. Pilihan A\\nB. Pilihan B"
            ];
        }

        // First line is question
        $question = trim(array_shift($lines));
        
        if (empty($question)) {
            $colName = $colIndex !== null ? chr(65 + $colIndex) : '';
            return [
                'error' => "Baris " . ($rowNumber ?? '?') . ($tipeNumber ? ", TIPE $tipeNumber" : '') . ($colName ? " (Kolom $colName)" : '') . ": Pertanyaan tidak boleh kosong"
            ];
        }

        // Parse options (format: "A. Pilihan A" atau "A) Pilihan A" atau "A - Pilihan A" atau "A. Pilihan A (keterangan)")
        $options = [];
        $optionMap = []; // Map untuk tracking urutan option (A, B, C, D)
        $correctAnswer = 'A'; // Default
        $foundCorrectAnswer = false;
        $unparsedLines = [];

        foreach ($lines as $lineIndex => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            
            // Match pattern: A. atau A) atau A - diikuti teks
            // Pattern lebih fleksibel untuk handle berbagai format
            if (preg_match('/^([A-D])[\.\)\-\s]+(.+)$/i', $line, $matches)) {
                $optionKey = strtoupper(trim($matches[1]));
                $optionText = trim($matches[2]);
                
                // Cek apakah ini jawaban benar (ditandai dengan *, [CORRECT], [BENAR], atau highlight)
                // Format: "A. Pilihan A *" atau "A. Pilihan A [CORRECT]" atau "A. Pilihan A [BENAR]"
                $isCorrect = false;
                if (preg_match('/\*+\s*$|\[CORRECT\]|\[BENAR\]|\[TRUE\]/i', $optionText)) {
                    $isCorrect = true;
                    $correctAnswer = $optionKey;
                    $foundCorrectAnswer = true;
                    // Remove tanda dari option text
                    $optionText = preg_replace('/\s*\*+\s*$|\s*\[CORRECT\]|\s*\[BENAR\]|\s*\[TRUE\]/i', '', $optionText);
                }
                
                // Remove keterangan dalam kurung jika ada (untuk format bilingual)
                // Contoh: "A. Memenuhi kriteria (Meeting required criteria)"
                // Kita ambil bagian sebelum kurung atau seluruhnya
                if (preg_match('/^(.+?)\s*\(.+\)\s*$/', $optionText, $textMatch)) {
                    $optionText = trim($textMatch[1]);
                }
                
                if (!empty($optionText)) {
                    $optionMap[$optionKey] = $optionText;
                    $options[] = $optionText;
                }
            } else {
                // Line tidak match pattern, simpan untuk error message
                $unparsedLines[] = "Baris " . ($lineIndex + 2) . ": '$line'";
            }
        }

        if (count($options) < 2) {
            $colName = $colIndex !== null ? chr(65 + $colIndex) : '';
            $errorMsg = "Baris " . ($rowNumber ?? '?') . ($tipeNumber ? ", TIPE $tipeNumber" : '') . ($colName ? " (Kolom $colName)" : '') . ": Minimal harus ada 2 pilihan jawaban (A dan B). Ditemukan: " . count($options) . " pilihan";
            
            if (!empty($unparsedLines)) {
                $errorMsg .= ". Baris yang tidak ter-parse: " . implode(', ', array_slice($unparsedLines, 0, 3));
                if (count($unparsedLines) > 3) {
                    $errorMsg .= '...';
                }
            }
            
            $errorMsg .= ". Format yang benar: A. Pilihan A\\nB. Pilihan B\\nC. Pilihan C\\nD. Pilihan D";
            
            return ['error' => $errorMsg];
        }

        // Reorder options berdasarkan A, B, C, D
        $orderedOptions = [];
        foreach (['A', 'B', 'C', 'D'] as $key) {
            if (isset($optionMap[$key])) {
                $orderedOptions[] = $optionMap[$key];
            }
        }
        
        // Jika ada option yang tidak terurut, gunakan urutan asli
        if (count($orderedOptions) < count($options)) {
            $orderedOptions = $options;
        }

        return [
            'question' => $question,
            'options' => $orderedOptions,
            'correct_answer' => $correctAnswer, // A, B, C, atau D (bisa dari tanda atau default A)
        ];
    }

    /**
     * Check if row is header row
     */
    protected function isHeaderRow($row)
    {
        $firstCell = strtoupper(trim($row[0] ?? ''));
        return in_array($firstCell, ['NO', 'NOMOR', 'NUMBER', '#']);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

