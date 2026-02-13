<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TrainingMaster;
use App\Models\TrainingSession;
use App\Models\TrainingMaterial;
use App\Models\TrainingMaterialCategory;
use App\Models\TrainingDifficultyLevel;
use App\Models\TrainingQuestionBank;
use App\Models\User;

class TrainingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== SEEDING TRAINING DATA ===');
        $this->command->newLine();

        // Get first user as creator
        $user = User::first();
        if (!$user) {
            $this->command->error('No user found! Please create a user first.');
            return;
        }

        // 1. Seed Difficulty Levels
        $this->command->info('1. Seeding Difficulty Levels...');
        $difficultyLevels = $this->seedDifficultyLevels($user->id);
        $this->command->info('   ✓ Difficulty levels created/updated');
        $this->command->newLine();

        // 2. Seed Material Categories
        $this->command->info('2. Seeding Material Categories...');
        $category = $this->seedMaterialCategories($user->id);
        $this->command->info('   ✓ Material categories created/updated');
        $this->command->newLine();

        // 3. Seed Materials
        $this->command->info('3. Seeding Materials...');
        $materials = $this->seedMaterials($category->id, $user->id);
        $this->command->info('   ✓ Materials created/updated');
        $this->command->newLine();

        // 4. Link Materials to Training Master (ID: 4 - RCCA)
        $this->command->info('4. Linking Materials to Training Master...');
        $training = TrainingMaster::find(4);
        if ($training) {
            $this->linkMaterialsToTraining($training, $materials);
            $this->command->info("   ✓ Materials linked to training: {$training->training_name}");
        } else {
            $this->command->warn('   ⚠ Training Master ID 4 not found. Skipping material linking.');
        }
        $this->command->newLine();

        // 5. Seed Question Banks
        $this->command->info('5. Seeding Question Banks...');
        $this->seedQuestionBanks($materials, $difficultyLevels, $user->id);
        $this->command->info('   ✓ Question banks created');
        $this->command->newLine();

        // 6. Update Session with Difficulty Level
        $this->command->info('6. Updating Session with Difficulty Level...');
        $this->updateSessionDifficultyLevel($difficultyLevels);
        $this->command->info('   ✓ Session updated');
        $this->command->newLine();

        $this->command->info('=== SEEDING COMPLETED ===');
        $this->command->info('You can now run: php artisan training:check 18 2');
    }

    /**
     * Seed difficulty levels
     */
    private function seedDifficultyLevels($userId)
    {
        $levels = [
            [
                'level_code' => 'VERY_EASY',
                'level_name' => 'Paling Mudah',
                'description' => 'Tingkat kesulitan paling mudah',
                'score_multiplier' => 1.0,
                'display_order' => 1,
            ],
            [
                'level_code' => 'EASY',
                'level_name' => 'Mudah',
                'description' => 'Tingkat kesulitan mudah',
                'score_multiplier' => 1.2,
                'display_order' => 2,
            ],
            [
                'level_code' => 'MEDIUM',
                'level_name' => 'Cukup',
                'description' => 'Tingkat kesulitan sedang',
                'score_multiplier' => 1.5,
                'display_order' => 3,
            ],
            [
                'level_code' => 'HARD',
                'level_name' => 'Menengah Ke Atas',
                'description' => 'Tingkat kesulitan menengah ke atas',
                'score_multiplier' => 2.0,
                'display_order' => 4,
            ],
            [
                'level_code' => 'VERY_HARD',
                'level_name' => 'Sulit',
                'description' => 'Tingkat kesulitan sulit',
                'score_multiplier' => 2.5,
                'display_order' => 5,
            ],
        ];

        $createdLevels = [];
        foreach ($levels as $level) {
            $difficultyLevel = TrainingDifficultyLevel::updateOrCreate(
                ['level_code' => $level['level_code']],
                array_merge($level, [
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'is_active' => true,
                ])
            );
            $createdLevels[$level['level_code']] = $difficultyLevel;
        }

        return $createdLevels;
    }

    /**
     * Seed material categories
     */
    private function seedMaterialCategories($userId)
    {
        $category = TrainingMaterialCategory::updateOrCreate(
            ['category_name' => 'RCCA Training'],
            [
                'description' => 'Kategori materi untuk training RCCA (Root Cause Corrective Action)',
                'display_order' => 1,
                'is_active' => true,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        return $category;
    }

    /**
     * Seed materials
     */
    private function seedMaterials($categoryId, $userId)
    {
        $materials = [
            [
                'material_title' => 'Pengenalan RCCA',
                'description' => 'Materi pengenalan tentang Root Cause Corrective Action (RCCA)',
                'video_path' => null,
                'display_order' => 1,
            ],
            [
                'material_title' => 'Metodologi RCCA',
                'description' => 'Materi tentang metodologi dan langkah-langkah RCCA',
                'video_path' => null,
                'display_order' => 2,
            ],
            [
                'material_title' => 'Implementasi RCCA',
                'description' => 'Materi tentang implementasi RCCA dalam praktik',
                'video_path' => null,
                'display_order' => 3,
            ],
        ];

        $createdMaterials = [];
        foreach ($materials as $material) {
            $createdMaterial = TrainingMaterial::updateOrCreate(
                [
                    'material_title' => $material['material_title'],
                    'category_id' => $categoryId,
                ],
                array_merge($material, [
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ])
            );
            $createdMaterials[] = $createdMaterial;
        }

        return $createdMaterials;
    }

    /**
     * Link materials to training master
     */
    private function linkMaterialsToTraining($training, $materials)
    {
        $materialIds = collect($materials)->pluck('id')->toArray();
        
        // Sync materials to training
        $syncData = [];
        foreach ($materials as $index => $material) {
            $syncData[$material->id] = [
                'display_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $training->materials()->sync($syncData);
    }

    /**
     * Seed question banks
     */
    private function seedQuestionBanks($materials, $difficultyLevels, $userId)
    {
        $questions = [
            // Material 1: Pengenalan RCCA
            [
                'material_index' => 0,
                'difficulty' => 'EASY',
                'question' => 'Apa kepanjangan dari RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Root Cause Corrective Action',
                'answer_options' => [
                    'A' => 'Root Cause Corrective Action',
                    'B' => 'Root Cause Corrective Analysis',
                    'C' => 'Root Cause Control Action',
                    'D' => 'Root Cause Control Analysis',
                ],
                'explanation' => 'RCCA adalah singkatan dari Root Cause Corrective Action yang berarti tindakan korektif berdasarkan akar penyebab masalah.',
                'score' => 10.00,
            ],
            [
                'material_index' => 0,
                'difficulty' => 'EASY',
                'question' => 'Apa tujuan utama dari RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Mengidentifikasi dan mengatasi akar penyebab masalah',
                'answer_options' => [
                    'A' => 'Mengidentifikasi dan mengatasi akar penyebab masalah',
                    'B' => 'Mencari kambing hitam untuk masalah',
                    'C' => 'Menyembunyikan masalah dari manajemen',
                    'D' => 'Mengurangi biaya operasional',
                ],
                'explanation' => 'Tujuan utama RCCA adalah mengidentifikasi akar penyebab masalah dan mengambil tindakan korektif yang tepat.',
                'score' => 10.00,
            ],
            [
                'material_index' => 0,
                'difficulty' => 'MEDIUM',
                'question' => 'Manakah yang BUKAN merupakan komponen utama RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Mencari kesalahan individu',
                'answer_options' => [
                    'A' => 'Identifikasi masalah',
                    'B' => 'Analisis akar penyebab',
                    'C' => 'Mencari kesalahan individu',
                    'D' => 'Tindakan korektif',
                ],
                'explanation' => 'RCCA fokus pada sistem dan proses, bukan mencari kesalahan individu. Mencari kesalahan individu bukanlah komponen RCCA.',
                'score' => 15.00,
            ],
            [
                'material_index' => 0,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa perbedaan antara gejala dan akar penyebab?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Gejala adalah tanda masalah, akar penyebab adalah penyebab sebenarnya',
                'answer_options' => [
                    'A' => 'Tidak ada perbedaan',
                    'B' => 'Gejala adalah tanda masalah, akar penyebab adalah penyebab sebenarnya',
                    'C' => 'Akar penyebab adalah gejala',
                    'D' => 'Gejala selalu lebih penting',
                ],
                'explanation' => 'Gejala adalah tanda atau indikator adanya masalah, sedangkan akar penyebab adalah penyebab sebenarnya yang perlu diatasi.',
                'score' => 15.00,
            ],
            [
                'material_index' => 0,
                'difficulty' => 'HARD',
                'question' => 'Mengapa penting untuk mengidentifikasi akar penyebab sebelum mengambil tindakan korektif?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Agar tindakan korektif efektif dan masalah tidak terulang',
                'answer_options' => [
                    'A' => 'Agar tindakan korektif efektif dan masalah tidak terulang',
                    'B' => 'Untuk menghemat waktu',
                    'C' => 'Karena aturan perusahaan',
                    'D' => 'Tidak penting, bisa langsung ambil tindakan',
                ],
                'explanation' => 'Mengidentifikasi akar penyebab penting agar tindakan korektif yang diambil tepat sasaran dan efektif mencegah masalah terulang.',
                'score' => 20.00,
            ],

            // Material 2: Metodologi RCCA
            [
                'material_index' => 1,
                'difficulty' => 'EASY',
                'question' => 'Metode 5 Why digunakan untuk apa?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Mengidentifikasi akar penyebab dengan bertanya "mengapa" secara berulang',
                'answer_options' => [
                    'A' => 'Mengidentifikasi akar penyebab dengan bertanya "mengapa" secara berulang',
                    'B' => 'Mencari 5 orang yang salah',
                    'C' => 'Mengurangi waktu investigasi',
                    'D' => 'Menyembunyikan masalah',
                ],
                'explanation' => 'Metode 5 Why adalah teknik untuk mengidentifikasi akar penyebab dengan bertanya "mengapa" secara berulang hingga menemukan penyebab sebenarnya.',
                'score' => 10.00,
            ],
            [
                'material_index' => 1,
                'difficulty' => 'EASY',
                'question' => 'Apa langkah pertama dalam proses RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Mengidentifikasi dan mendeskripsikan masalah',
                'answer_options' => [
                    'A' => 'Mengidentifikasi dan mendeskripsikan masalah',
                    'B' => 'Mencari siapa yang salah',
                    'C' => 'Mengambil tindakan korektif',
                    'D' => 'Menyusun laporan',
                ],
                'explanation' => 'Langkah pertama dalam RCCA adalah mengidentifikasi dan mendeskripsikan masalah dengan jelas dan spesifik.',
                'score' => 10.00,
            ],
            [
                'material_index' => 1,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa yang dimaksud dengan Fishbone Diagram (Ishikawa Diagram)?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Diagram untuk menganalisis akar penyebab masalah secara visual',
                'answer_options' => [
                    'A' => 'Diagram untuk menganalisis akar penyebab masalah secara visual',
                    'B' => 'Diagram untuk menghitung biaya',
                    'C' => 'Diagram untuk jadwal produksi',
                    'D' => 'Diagram untuk struktur organisasi',
                ],
                'explanation' => 'Fishbone Diagram atau Ishikawa Diagram adalah alat visual untuk menganalisis dan mengorganisir kemungkinan penyebab masalah.',
                'score' => 15.00,
            ],
            [
                'material_index' => 1,
                'difficulty' => 'MEDIUM',
                'question' => 'Kapan waktu yang tepat untuk melakukan RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Ketika terjadi masalah yang signifikan atau berulang',
                'answer_options' => [
                    'A' => 'Hanya saat diminta manajemen',
                    'B' => 'Ketika terjadi masalah yang signifikan atau berulang',
                    'C' => 'Setiap hari',
                    'D' => 'Hanya saat audit',
                ],
                'explanation' => 'RCCA sebaiknya dilakukan ketika terjadi masalah yang signifikan, berulang, atau memiliki dampak besar terhadap operasional.',
                'score' => 15.00,
            ],
            [
                'material_index' => 1,
                'difficulty' => 'HARD',
                'question' => 'Apa perbedaan antara corrective action dan preventive action?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Corrective action mengatasi masalah yang sudah terjadi, preventive action mencegah masalah terjadi',
                'answer_options' => [
                    'A' => 'Tidak ada perbedaan',
                    'B' => 'Corrective action mengatasi masalah yang sudah terjadi, preventive action mencegah masalah terjadi',
                    'C' => 'Preventive action lebih mahal',
                    'D' => 'Corrective action tidak efektif',
                ],
                'explanation' => 'Corrective action adalah tindakan untuk mengatasi masalah yang sudah terjadi, sedangkan preventive action adalah tindakan untuk mencegah masalah terjadi di masa depan.',
                'score' => 20.00,
            ],

            // Material 3: Implementasi RCCA
            [
                'material_index' => 2,
                'difficulty' => 'EASY',
                'question' => 'Siapa yang sebaiknya terlibat dalam tim RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Orang yang memahami proses dan masalah',
                'answer_options' => [
                    'A' => 'Hanya manajer',
                    'B' => 'Orang yang memahami proses dan masalah',
                    'C' => 'Hanya staf HR',
                    'D' => 'Hanya auditor eksternal',
                ],
                'explanation' => 'Tim RCCA sebaiknya terdiri dari orang-orang yang memahami proses dan masalah yang sedang dianalisis.',
                'score' => 10.00,
            ],
            [
                'material_index' => 2,
                'difficulty' => 'EASY',
                'question' => 'Apa yang harus dilakukan setelah mengidentifikasi akar penyebab?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Mengembangkan dan mengimplementasikan tindakan korektif',
                'answer_options' => [
                    'A' => 'Mengembangkan dan mengimplementasikan tindakan korektif',
                    'B' => 'Menyalahkan orang lain',
                    'C' => 'Mengabaikan masalah',
                    'D' => 'Menunggu instruksi',
                ],
                'explanation' => 'Setelah mengidentifikasi akar penyebab, langkah selanjutnya adalah mengembangkan dan mengimplementasikan tindakan korektif yang tepat.',
                'score' => 10.00,
            ],
            [
                'material_index' => 2,
                'difficulty' => 'MEDIUM',
                'question' => 'Mengapa penting untuk memverifikasi efektivitas tindakan korektif?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Untuk memastikan masalah benar-benar teratasi',
                'answer_options' => [
                    'A' => 'Untuk memastikan masalah benar-benar teratasi',
                    'B' => 'Karena aturan perusahaan',
                    'C' => 'Untuk menghemat biaya',
                    'D' => 'Tidak penting',
                ],
                'explanation' => 'Verifikasi efektivitas tindakan korektif penting untuk memastikan bahwa masalah benar-benar teratasi dan tidak akan terulang.',
                'score' => 15.00,
            ],
            [
                'material_index' => 2,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa yang harus dicantumkan dalam laporan RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Deskripsi masalah, akar penyebab, dan tindakan korektif',
                'answer_options' => [
                    'A' => 'Hanya nama orang yang salah',
                    'B' => 'Deskripsi masalah, akar penyebab, dan tindakan korektif',
                    'C' => 'Hanya biaya yang dikeluarkan',
                    'D' => 'Hanya tanggal kejadian',
                ],
                'explanation' => 'Laporan RCCA yang baik harus mencakup deskripsi masalah, analisis akar penyebab, dan rencana tindakan korektif.',
                'score' => 15.00,
            ],
            [
                'material_index' => 2,
                'difficulty' => 'HARD',
                'question' => 'Bagaimana cara memastikan tindakan korektif berkelanjutan?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Dengan monitoring berkala dan dokumentasi yang baik',
                'answer_options' => [
                    'A' => 'Dengan monitoring berkala dan dokumentasi yang baik',
                    'B' => 'Dengan mengabaikan masalah',
                    'C' => 'Dengan menyalahkan orang lain',
                    'D' => 'Dengan tidak melakukan apa-apa',
                ],
                'explanation' => 'Tindakan korektif berkelanjutan dapat dipastikan melalui monitoring berkala dan dokumentasi yang baik untuk memastikan implementasi yang konsisten.',
                'score' => 20.00,
            ],

            // Tambahan pertanyaan untuk memastikan cukup 10+ pertanyaan dengan difficulty MEDIUM
            [
                'material_index' => 0,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa yang dimaksud dengan "root cause" dalam konteks RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Penyebab fundamental yang jika diatasi akan mencegah masalah terulang',
                'answer_options' => [
                    'A' => 'Penyebab fundamental yang jika diatasi akan mencegah masalah terulang',
                    'B' => 'Gejala yang terlihat',
                    'C' => 'Orang yang menyebabkan masalah',
                    'D' => 'Masalah yang terjadi',
                ],
                'explanation' => 'Root cause adalah penyebab fundamental atau dasar yang jika diatasi akan mencegah masalah terulang di masa depan.',
                'score' => 15.00,
            ],
            [
                'material_index' => 1,
                'difficulty' => 'MEDIUM',
                'question' => 'Berapa kali idealnya bertanya "mengapa" dalam metode 5 Why?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Sampai menemukan akar penyebab, biasanya 5 kali',
                'answer_options' => [
                    'A' => 'Tepat 5 kali, tidak boleh lebih atau kurang',
                    'B' => 'Sampai menemukan akar penyebab, biasanya 5 kali',
                    'C' => 'Hanya 3 kali',
                    'D' => 'Sesuai keinginan',
                ],
                'explanation' => 'Metode 5 Why tidak harus tepat 5 kali, tetapi dilakukan sampai menemukan akar penyebab yang sebenarnya. Biasanya membutuhkan sekitar 5 kali pertanyaan.',
                'score' => 15.00,
            ],
            [
                'material_index' => 2,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa yang harus dilakukan jika tindakan korektif tidak efektif?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Menganalisis ulang dan mengembangkan tindakan korektif baru',
                'answer_options' => [
                    'A' => 'Mengabaikan masalah',
                    'B' => 'Menganalisis ulang dan mengembangkan tindakan korektif baru',
                    'C' => 'Menyalahkan tim',
                    'D' => 'Tidak melakukan apa-apa',
                ],
                'explanation' => 'Jika tindakan korektif tidak efektif, perlu dilakukan analisis ulang untuk memahami mengapa tidak efektif dan mengembangkan tindakan korektif yang baru.',
                'score' => 15.00,
            ],
            [
                'material_index' => 0,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa yang membedakan RCCA dengan pendekatan reaktif biasa?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'RCCA mencari akar penyebab, pendekatan reaktif hanya mengatasi gejala',
                'answer_options' => [
                    'A' => 'Tidak ada perbedaan',
                    'B' => 'RCCA mencari akar penyebab, pendekatan reaktif hanya mengatasi gejala',
                    'C' => 'RCCA lebih cepat',
                    'D' => 'RCCA lebih murah',
                ],
                'explanation' => 'RCCA berbeda dengan pendekatan reaktif karena fokus pada mencari dan mengatasi akar penyebab, bukan hanya mengatasi gejala masalah.',
                'score' => 15.00,
            ],
            [
                'material_index' => 1,
                'difficulty' => 'MEDIUM',
                'question' => 'Apa keuntungan menggunakan Fishbone Diagram dalam analisis RCCA?',
                'question_type' => 'multiple_choice',
                'correct_answer' => 'Membantu mengorganisir dan memvisualisasikan semua kemungkinan penyebab',
                'answer_options' => [
                    'A' => 'Membantu mengorganisir dan memvisualisasikan semua kemungkinan penyebab',
                    'B' => 'Menghemat biaya',
                    'C' => 'Mengurangi waktu analisis',
                    'D' => 'Tidak ada keuntungan',
                ],
                'explanation' => 'Fishbone Diagram membantu mengorganisir dan memvisualisasikan semua kemungkinan penyebab masalah secara sistematis.',
                'score' => 15.00,
            ],
        ];

        foreach ($questions as $questionData) {
            $material = $materials[$questionData['material_index']];
            $difficultyLevel = $difficultyLevels[$questionData['difficulty']];

            TrainingQuestionBank::updateOrCreate(
                [
                    'question' => $questionData['question'],
                    'material_id' => $material->id,
                ],
                [
                    'question_type' => $questionData['question_type'],
                    'difficulty_level_id' => $difficultyLevel->id,
                    'correct_answer' => $questionData['correct_answer'],
                    'answer_options' => $questionData['answer_options'],
                    'explanation' => $questionData['explanation'],
                    'score' => $questionData['score'],
                    'is_active' => true,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        $this->command->info("   Created " . count($questions) . " questions");
    }

    /**
     * Update session with difficulty level
     */
    private function updateSessionDifficultyLevel($difficultyLevels)
    {
        $session = TrainingSession::find(2);
        if ($session) {
            // Use MEDIUM difficulty level as default
            $mediumLevel = $difficultyLevels['MEDIUM'] ?? $difficultyLevels['EASY'];
            
            $session->update([
                'difficulty_level_id' => $mediumLevel->id,
            ]);
            
            $this->command->info("   Session ID 2 updated with difficulty level: {$mediumLevel->level_name}");
        } else {
            $this->command->warn('   ⚠ Session ID 2 not found. Skipping update.');
        }
    }
}

