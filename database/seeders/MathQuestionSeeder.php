<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MathQuestion;

class MathQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question_number' => 1,
                'question' => '1, 3, 4, 7, 11, 18, 29,…….',
                'answer' => '47',
                'question_type' => 'pattern',
                'difficulty_level' => 2,
                'explanation' => 'Pola: setiap angka adalah penjumlahan dua angka sebelumnya (1+3=4, 3+4=7, 4+7=11, 7+11=18, 11+18=29, 18+29=47)',
            ],
            [
                'question_number' => 2,
                'question' => '4, 5, 8, 15, 16, 45, 32, …….',
                'answer' => '135',
                'question_type' => 'pattern',
                'difficulty_level' => 3,
                'explanation' => 'Pola: posisi ganjil dikalikan 2, posisi genap dikalikan 3 (4×2=8, 5×3=15, 8×2=16, 15×3=45, 16×2=32, 45×3=135)',
            ],
            [
                'question_number' => 3,
                'question' => '5, 10, 7, 12, 9, …….',
                'answer' => '14',
                'question_type' => 'pattern',
                'difficulty_level' => 2,
                'explanation' => 'Pola: posisi ganjil +2 (5,7,9), posisi genap +2 (10,12,14)',
            ],
            [
                'question_number' => 4,
                'question' => '8, 64, 16, 32, 32, 16, 64, 8, ……',
                'answer' => '128',
                'question_type' => 'pattern',
                'difficulty_level' => 3,
                'explanation' => 'Pola: 8, 64, 16, 32, 32, 16, 64, 8, 128 (pola simetris dan perkalian)',
            ],
            [
                'question_number' => 5,
                'question' => '12, 6, 9, 5, 6, 4, 3, ……',
                'answer' => '3',
                'question_type' => 'pattern',
                'difficulty_level' => 3,
                'explanation' => 'Pola: posisi ganjil -3 (12,9,6,3), posisi genap -1 (6,5,4,3)',
            ],
            [
                'question_number' => 6,
                'question' => 'Nilai 397 x 397 + 104 x 104 + 2 x 397 x 104 = …….',
                'answer' => '251001',
                'question_type' => 'calculation',
                'difficulty_level' => 3,
                'explanation' => 'Rumus: (a+b)² = a² + b² + 2ab = (397+104)² = 501² = 251001',
            ],
            [
                'question_number' => 7,
                'question' => '53 + 53 + 53 + 53 + 53 = 5n, maka nilai n adalah …….',
                'answer' => '4',
                'question_type' => 'calculation',
                'difficulty_level' => 2,
                'explanation' => '5 × 5³ = 5⁴, maka n = 4',
            ],
            [
                'question_number' => 8,
                'question' => '32 + 24 : 6 - 10 x 2 = …….',
                'answer' => '16',
                'question_type' => 'calculation',
                'difficulty_level' => 2,
                'explanation' => '32 + (24:6) - (10×2) = 32 + 4 - 20 = 16',
            ],
            [
                'question_number' => 9,
                'question' => '2 milenium = ……. tahun',
                'answer' => '2000',
                'question_type' => 'conversion',
                'difficulty_level' => 1,
                'explanation' => '1 milenium = 1000 tahun, maka 2 milenium = 2000 tahun',
            ],
            [
                'question_number' => 10,
                'question' => '1 triwulan = …… bulan',
                'answer' => '3',
                'question_type' => 'conversion',
                'difficulty_level' => 1,
                'explanation' => '1 triwulan = 3 bulan',
            ],
            [
                'question_number' => 11,
                'question' => '2 windu = …… tahun',
                'answer' => '16',
                'question_type' => 'conversion',
                'difficulty_level' => 1,
                'explanation' => '1 windu = 8 tahun, maka 2 windu = 16 tahun',
            ],
            [
                'question_number' => 12,
                'question' => '1 km = …… dm',
                'answer' => '10000',
                'question_type' => 'conversion',
                'difficulty_level' => 2,
                'explanation' => '1 km = 10.000 dm',
            ],
            [
                'question_number' => 13,
                'question' => '5 Liter = ….. ml',
                'answer' => '5000',
                'question_type' => 'conversion',
                'difficulty_level' => 1,
                'explanation' => '1 Liter = 1000 ml, maka 5 Liter = 5000 ml',
            ],
            [
                'question_number' => 14,
                'question' => '3 Lusin = ….. biji',
                'answer' => '36',
                'question_type' => 'conversion',
                'difficulty_level' => 1,
                'explanation' => '1 lusin = 12 biji, maka 3 lusin = 36 biji',
            ],
            [
                'question_number' => 15,
                'question' => '2 ton = ….. kg',
                'answer' => '2000',
                'question_type' => 'conversion',
                'difficulty_level' => 1,
                'explanation' => '1 ton = 1000 kg, maka 2 ton = 2000 kg',
            ],
            [
                'question_number' => 16,
                'question' => '¾ + ½ = ……..',
                'answer' => '1 1/4,1.25,5/4',
                'question_type' => 'calculation',
                'difficulty_level' => 2,
                'explanation' => '3/4 + 1/2 = 3/4 + 2/4 = 5/4 = 1 1/4 = 1.25',
            ],
            [
                'question_number' => 17,
                'question' => 'Nasti membeli tepung, margarin, dan gula total seharga Rp 100.000. Berat ketiga bahan kue tersebut 9kg. Nilai belanja tepung setengah dari nilai belanja margarin. Jika harga setiap kg dari tepung, margarin, dan gula berturut-turut adalah Rp 6.000, Rp 30.000, dan Rp 5.000. berapa kg gula yang dibeli oleh Nasti?',
                'answer' => '2',
                'question_type' => 'word_problem',
                'difficulty_level' => 4,
                'explanation' => 'Misal: tepung = t kg, margarin = m kg, gula = g kg. t + m + g = 9. Harga tepung = 6000t, margarin = 30000m, gula = 5000g. 6000t = 0.5 × 30000m = 15000m, maka t = 2.5m. 6000(2.5m) + 30000m + 5000g = 100000. 15000m + 30000m + 5000g = 100000. 45000m + 5000g = 100000. 2.5m + m + g = 9, maka 3.5m + g = 9, g = 9 - 3.5m. Substitusi: 45000m + 5000(9-3.5m) = 100000. 45000m + 45000 - 17500m = 100000. 27500m = 55000, m = 2. g = 9 - 3.5(2) = 9 - 7 = 2 kg',
            ],
            [
                'question_number' => 18,
                'question' => 'Umur Ulfa 1/3 kali umur ayahnya, Umur ibunya 5/6 kali umur ayahnya. Jika Umur Ulfa 18 tahun, maka umur ibunya adalah?',
                'answer' => '45',
                'question_type' => 'word_problem',
                'difficulty_level' => 3,
                'explanation' => 'Ulfa = 1/3 × Ayah = 18, maka Ayah = 54 tahun. Ibu = 5/6 × 54 = 45 tahun',
            ],
            [
                'question_number' => 19,
                'question' => 'Jika 5% dari suatu bilangan adalah 6, maka 20% dari bilangan tersebut adalah ……',
                'answer' => '24',
                'question_type' => 'word_problem',
                'difficulty_level' => 2,
                'explanation' => '5% × bilangan = 6, maka bilangan = 120. 20% × 120 = 24',
            ],
            [
                'question_number' => 20,
                'question' => 'Persegi panjang memiliki luas 120cm², jika p dari persegi panjang tersebut adalah 15cm, berapa l dari persegi panjang tersebut?',
                'answer' => '8',
                'question_type' => 'word_problem',
                'difficulty_level' => 2,
                'explanation' => 'Luas = p × l. 120 = 15 × l. l = 120/15 = 8 cm',
            ],
        ];

        foreach ($questions as $questionData) {
            MathQuestion::updateOrCreate(
                ['question_number' => $questionData['question_number']],
                $questionData
            );
        }
    }
}

