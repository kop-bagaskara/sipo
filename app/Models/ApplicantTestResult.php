<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantTestResult extends Model
{
    use HasFactory;

    protected $table = 'tb_applicant_test_results';
    protected $connection = 'pgsql2';

    protected $fillable = [
        'applicant_id',
        'test_type',
        'test_name',
        'score',
        'max_score',
        'answers',
        'screenshot_path',
        'hrd_status',
        'hrd_notes',
        'hrd_confirmed_by',
        'hrd_confirmed_at',
        'test_date',
        'duration_minutes',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'answers' => 'array',
        'test_date' => 'datetime',
        'hrd_confirmed_at' => 'datetime',
    ];

    // Relationship dengan HRD yang mengkonfirmasi
    public function hrdConfirmedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'hrd_confirmed_by', 'id');
    }

    // Relationship dengan applicant
    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }

    // Accessor untuk persentase skor
    public function getScorePercentageAttribute()
    {
        if ($this->max_score > 0) {
            return round(($this->score / $this->max_score) * 100, 2);
        }
        return 0;
    }

    // Accessor untuk status test
    public function getTestStatusAttribute()
    {
        $percentage = $this->score_percentage;

        if ($percentage >= 80) {
            return 'Excellent';
        } elseif ($percentage >= 70) {
            return 'Good';
        } elseif ($percentage >= 60) {
            return 'Average';
        } else {
            return 'Poor';
        }
    }

    /**
     * Klasifikasi hasil test Ishihara (Buta Warna) berdasarkan standar medis
     * Referensi: https://www.challengetb.org/publications/tools/country/Ishihara_Tests.pdf
     *
     * @return array
     */
    public function getIshiharaClassificationAttribute()
    {
        // Hanya untuk test buta warna (test_3)
        if ($this->test_type !== 'test_3') {
            return null;
        }

        $correctAnswers = $this->score;
        $totalPlates = $this->max_score;
        $percentage = $this->score_percentage;

        // Algoritma klasifikasi berdasarkan jumlah jawaban benar
        // Untuk test 20 plates (atau variasi jumlah plates)
        if ($totalPlates >= 17 && $totalPlates <= 25) {
            // Test standar 17-25 plates
            if ($correctAnswers >= 17) {
                return [
                    'category' => 'normal',
                    'label' => 'Normal',
                    'description' => 'Penglihatan warna normal',
                    'indonesian' => 'Penglihatan warna normal',
                    'color' => 'success',
                    'severity' => 0,
                    'recommendation' => 'Tidak ada masalah penglihatan warna. Memenuhi syarat untuk posisi yang memerlukan penglihatan warna normal.'
                ];
            } elseif ($correctAnswers >= 13 && $correctAnswers <= 16) {
                return [
                    'category' => 'mild',
                    'label' => 'Mild Color Blindness',
                    'description' => 'Buta warna ringan (Deuteranomaly/Protanomaly ringan)',
                    'indonesian' => 'Buta Warna Ringan',
                    'color' => 'warning',
                    'severity' => 1,
                    'recommendation' => 'Memiliki buta warna ringan. Masih dapat membedakan warna dasar dengan baik, namun mungkin kesulitan dengan nuansa warna tertentu.'
                ];
            } elseif ($correctAnswers >= 9 && $correctAnswers <= 12) {
                return [
                    'category' => 'moderate',
                    'label' => 'Moderate Color Blindness',
                    'description' => 'Buta warna sedang (Deuteranomaly/Protanomaly sedang)',
                    'indonesian' => 'Buta Warna Sedang',
                    'color' => 'warning',
                    'severity' => 2,
                    'recommendation' => 'Memiliki buta warna sedang. Kesulitan membedakan beberapa warna, terutama merah-hijau. Perlu evaluasi lebih lanjut untuk posisi yang memerlukan penglihatan warna akurat.'
                ];
            } elseif ($correctAnswers >= 5 && $correctAnswers <= 8) {
                return [
                    'category' => 'severe',
                    'label' => 'Severe Color Blindness',
                    'description' => 'Buta warna parah (Deuteranopia/Protanopia)',
                    'indonesian' => 'Buta Warna Parah',
                    'color' => 'danger',
                    'severity' => 3,
                    'recommendation' => 'Memiliki buta warna parah. Kesulitan besar dalam membedakan warna merah-hijau. Tidak disarankan untuk posisi yang memerlukan penglihatan warna akurat.'
                ];
            } else {
                return [
                    'category' => 'total',
                    'label' => 'Total Color Blindness',
                    'description' => 'Buta warna total (Achromatopsia atau buta warna sangat parah)',
                    'indonesian' => 'Buta Warna Total',
                    'color' => 'danger',
                    'severity' => 4,
                    'recommendation' => 'Memiliki buta warna total atau sangat parah. Hanya melihat dalam skala abu-abu atau kesulitan ekstrem membedakan warna. Tidak memenuhi syarat untuk posisi yang memerlukan penglihatan warna.'
                ];
            }
        } else {
            // Untuk test dengan jumlah plates yang berbeda, gunakan persentase
            if ($percentage >= 85) {
                return [
                    'category' => 'normal',
                    'label' => 'Normal',
                    'description' => 'Penglihatan warna normal',
                    'indonesian' => 'Penglihatan warna normal',
                    'color' => 'success',
                    'severity' => 0,
                    'recommendation' => 'Tidak ada masalah penglihatan warna.'
                ];
            } elseif ($percentage >= 65 && $percentage < 85) {
                return [
                    'category' => 'mild',
                    'label' => 'Mild Color Blindness',
                    'description' => 'Buta warna ringan',
                    'indonesian' => 'Buta Warna Ringan',
                    'color' => 'warning',
                    'severity' => 1,
                    'recommendation' => 'Memiliki buta warna ringan. Masih dapat membedakan warna dasar dengan baik.'
                ];
            } elseif ($percentage >= 45 && $percentage < 65) {
                return [
                    'category' => 'moderate',
                    'label' => 'Moderate Color Blindness',
                    'description' => 'Buta warna sedang',
                    'indonesian' => 'Buta Warna Sedang',
                    'color' => 'warning',
                    'severity' => 2,
                    'recommendation' => 'Memiliki buta warna sedang. Perlu evaluasi lebih lanjut.'
                ];
            } elseif ($percentage >= 25 && $percentage < 45) {
                return [
                    'category' => 'severe',
                    'label' => 'Severe Color Blindness',
                    'description' => 'Buta warna parah',
                    'indonesian' => 'Buta Warna Parah',
                    'color' => 'danger',
                    'severity' => 3,
                    'recommendation' => 'Memiliki buta warna parah. Tidak disarankan untuk posisi yang memerlukan penglihatan warna akurat.'
                ];
            } else {
                return [
                    'category' => 'total',
                    'label' => 'Total Color Blindness',
                    'description' => 'Buta warna total',
                    'indonesian' => 'Buta Warna Total',
                    'color' => 'danger',
                    'severity' => 4,
                    'recommendation' => 'Memiliki buta warna total. Tidak memenuhi syarat untuk posisi yang memerlukan penglihatan warna.'
                ];
            }
        }
    }

    // Accessor untuk format nama test
    public function getTestNameFormattedAttribute()
    {
        $testNames = [
            'test_1' => 'Tes Matematika',
            'test_2' => 'Tes Krapelin',
            'test_3' => 'Tes Buta Warna',
            'test_4' => 'Tes Kepribadian'
        ];

        return $testNames[$this->test_type] ?? $this->test_name;
    }

    // Scope untuk filter berdasarkan jenis test
    public function scopeByTestType($query, $testType)
    {
        return $query->where('test_type', $testType);
    }

    // Scope untuk filter berdasarkan skor minimum
    public function scopeByMinScore($query, $minScore)
    {
        return $query->where('score', '>=', $minScore);
    }

    /**
     * Klasifikasi hasil test Matematika berdasarkan skor
     *
     * @return array|null
     */
    public function getMathClassificationAttribute()
    {
        // Hanya untuk test matematika (test_1)
        if ($this->test_type !== 'test_1') {
            return null;
        }

        $score = $this->score;
        $maxScore = $this->max_score;
        $percentage = $this->score_percentage;

        // Algoritma klasifikasi berdasarkan persentase skor
        if ($percentage >= 90) {
            return [
                'category' => 'excellent',
                'label' => 'Excellent',
                'description' => 'Sangat Baik',
                'indonesian' => 'Sangat Baik',
                'color' => 'success',
                'grade' => 'A',
                'severity' => 0,
                'recommendation' => 'Kemampuan matematika sangat baik. Memiliki pemahaman yang kuat dalam berbagai konsep matematika. Sangat cocok untuk posisi yang memerlukan kemampuan analitis dan pemecahan masalah matematis.'
            ];
        } elseif ($percentage >= 80 && $percentage < 90) {
            return [
                'category' => 'very_good',
                'label' => 'Very Good',
                'description' => 'Baik Sekali',
                'indonesian' => 'Baik Sekali',
                'color' => 'success',
                'grade' => 'B+',
                'severity' => 0,
                'recommendation' => 'Kemampuan matematika baik sekali. Memiliki pemahaman yang solid dalam berbagai konsep matematika. Cocok untuk posisi yang memerlukan kemampuan analitis.'
            ];
        } elseif ($percentage >= 70 && $percentage < 80) {
            return [
                'category' => 'good',
                'label' => 'Good',
                'description' => 'Baik',
                'indonesian' => 'Baik',
                'color' => 'info',
                'grade' => 'B',
                'severity' => 1,
                'recommendation' => 'Kemampuan matematika baik. Memiliki pemahaman dasar yang cukup dalam konsep matematika. Dapat mengikuti pelatihan tambahan jika diperlukan untuk posisi tertentu.'
            ];
        } elseif ($percentage >= 60 && $percentage < 70) {
            return [
                'category' => 'average',
                'label' => 'Average',
                'description' => 'Cukup',
                'indonesian' => 'Cukup',
                'color' => 'warning',
                'grade' => 'C+',
                'severity' => 2,
                'recommendation' => 'Kemampuan matematika cukup. Memahami konsep dasar matematika namun perlu penguatan lebih lanjut. Perlu evaluasi lebih lanjut untuk posisi yang memerlukan kemampuan matematika tingkat menengah.'
            ];
        } elseif ($percentage >= 50 && $percentage < 60) {
            return [
                'category' => 'below_average',
                'label' => 'Below Average',
                'description' => 'Kurang',
                'indonesian' => 'Kurang',
                'color' => 'warning',
                'grade' => 'C',
                'severity' => 3,
                'recommendation' => 'Kemampuan matematika kurang. Memiliki pemahaman dasar namun masih perlu peningkatan. Disarankan untuk pelatihan tambahan sebelum mempertimbangkan posisi yang memerlukan kemampuan matematika.'
            ];
        } else {
            return [
                'category' => 'poor',
                'label' => 'Poor',
                'description' => 'Sangat Kurang',
                'indonesian' => 'Sangat Kurang',
                'color' => 'danger',
                'grade' => 'D',
                'severity' => 4,
                'recommendation' => 'Kemampuan matematika sangat kurang. Memerlukan pelatihan intensif dan pengembangan lebih lanjut. Tidak disarankan untuk posisi yang memerlukan kemampuan matematika tingkat menengah ke atas tanpa pelatihan terlebih dahulu.'
            ];
        }
    }

    /**
     * Klasifikasi hasil test Krapelin berdasarkan skor
     * Test Krapelin mengukur: ketelitian, konsentrasi, ketahanan bekerja di bawah tekanan waktu
     *
     * @return array|null
     */
    public function getKraepelinClassificationAttribute()
    {
        // Hanya untuk test Krapelin (test_2)
        if ($this->test_type !== 'test_2') {
            return null;
        }

        $score = $this->score;
        $maxScore = $this->max_score;
        $percentage = $this->score_percentage;
        $durationMinutes = $this->duration_minutes ?? 1;

        // Hitung kecepatan (soal benar per menit) - Kelincahan
        $speed = $durationMinutes > 0 ? round($score / $durationMinutes, 2) : 0;

        // Hitung Kelincahan (Agility/Speed) - soal per menit
        $kelincahan = $speed;
        $kelincahanLabel = $this->getKelincahanLabel($speed);

        // Hitung Ketelitian (Accuracy) - persentase benar
        $ketelitian = $percentage;
        $ketelitianLabel = $this->getKetelitianLabel($percentage);

        // Hitung Konsentrasi - berdasarkan kombinasi akurasi dan kecepatan
        $konsentrasi = ($percentage * 0.6) + (min($speed / 50 * 100, 100) * 0.4);
        $konsentrasiLabel = $this->getKonsentrasiLabel($konsentrasi);

        // Hitung Ketahanan - kemampuan bekerja di bawah tekanan waktu
        $ketahanan = ($percentage * 0.5) + (min($speed / 40 * 100, 100) * 0.5);
        $ketahananLabel = $this->getKetahananLabel($ketahanan);

        // Helper untuk membuat aspects array
        $aspects = [
            'kelincahan' => [
                'value' => round($kelincahan, 2),
                'label' => $kelincahanLabel,
                'description' => 'Kecepatan dalam menyelesaikan soal (soal per menit)'
            ],
            'ketelitian' => [
                'value' => round($ketelitian, 2),
                'label' => $ketelitianLabel,
                'description' => 'Tingkat akurasi dalam menjawab soal (persentase benar)'
            ],
            'konsentrasi' => [
                'value' => round($konsentrasi, 2),
                'label' => $konsentrasiLabel,
                'description' => 'Kemampuan fokus dan konsistensi dalam menjawab'
            ],
            'ketahanan' => [
                'value' => round($ketahanan, 2),
                'label' => $ketahananLabel,
                'description' => 'Kemampuan bekerja di bawah tekanan waktu'
            ]
        ];

        // Kombinasi akurasi dan kecepatan untuk klasifikasi
        // Test Krapelin mengukur kombinasi ketelitian (akurasi) dan kecepatan
        if ($percentage >= 90 && $speed >= 40) {
            return [
                'category' => 'excellent',
                'label' => 'Excellent',
                'description' => 'Sangat Baik',
                'indonesian' => 'Sangat Baik',
                'color' => 'success',
                'grade' => 'A',
                'severity' => 0,
                'recommendation' => 'Kemampuan ketelitian dan kecepatan sangat baik. Memiliki konsentrasi tinggi dan ketahanan bekerja di bawah tekanan waktu yang sangat baik. Sangat cocok untuk posisi yang memerlukan ketelitian tinggi dan kemampuan bekerja dengan cepat.',
                'aspects' => $aspects
            ];
        } elseif ($percentage >= 80 && $percentage < 90 && $speed >= 35) {
            return [
                'category' => 'very_good',
                'label' => 'Very Good',
                'description' => 'Baik Sekali',
                'indonesian' => 'Baik Sekali',
                'color' => 'success',
                'grade' => 'B+',
                'severity' => 0,
                'recommendation' => 'Kemampuan ketelitian dan kecepatan baik sekali. Memiliki konsentrasi yang solid dan ketahanan bekerja di bawah tekanan waktu yang baik. Cocok untuk posisi yang memerlukan ketelitian dan kecepatan.',
                'aspects' => $aspects
            ];
        } elseif ($percentage >= 70 && $percentage < 80 && $speed >= 30) {
            return [
                'category' => 'good',
                'label' => 'Good',
                'description' => 'Baik',
                'indonesian' => 'Baik',
                'color' => 'info',
                'grade' => 'B',
                'severity' => 1,
                'recommendation' => 'Kemampuan ketelitian dan kecepatan baik. Memiliki konsentrasi yang cukup dan dapat bekerja di bawah tekanan waktu. Dapat mengikuti pelatihan tambahan untuk meningkatkan kecepatan dan ketelitian lebih lanjut.',
                'aspects' => $aspects
            ];
        } elseif ($percentage >= 60 && $percentage < 70 && $speed >= 25) {
            return [
                'category' => 'average',
                'label' => 'Average',
                'description' => 'Cukup',
                'indonesian' => 'Cukup',
                'color' => 'warning',
                'grade' => 'C+',
                'severity' => 2,
                'recommendation' => 'Kemampuan ketelitian dan kecepatan cukup. Memiliki konsentrasi dasar namun perlu penguatan lebih lanjut. Perlu evaluasi lebih lanjut untuk posisi yang memerlukan ketelitian dan kecepatan tinggi.',
                'aspects' => $aspects
            ];
        } elseif ($percentage >= 50 && $percentage < 60) {
            return [
                'category' => 'below_average',
                'label' => 'Below Average',
                'description' => 'Kurang',
                'indonesian' => 'Kurang',
                'color' => 'warning',
                'grade' => 'C',
                'severity' => 3,
                'recommendation' => 'Kemampuan ketelitian dan kecepatan kurang. Memiliki konsentrasi dasar namun masih perlu peningkatan signifikan. Disarankan untuk pelatihan tambahan sebelum mempertimbangkan posisi yang memerlukan ketelitian dan kecepatan tinggi.',
                'aspects' => $aspects
            ];
        } else {
            return [
                'category' => 'poor',
                'label' => 'Poor',
                'description' => 'Sangat Kurang',
                'indonesian' => 'Sangat Kurang',
                'color' => 'danger',
                'grade' => 'D',
                'severity' => 4,
                'recommendation' => 'Kemampuan ketelitian dan kecepatan sangat kurang. Memerlukan pelatihan intensif dan pengembangan lebih lanjut. Tidak disarankan untuk posisi yang memerlukan ketelitian dan kecepatan tinggi tanpa pelatihan terlebih dahulu.',
                'aspects' => $aspects
            ];
        }
    }

    /**
     * Helper function untuk mendapatkan label kelincahan
     */
    private function getKelincahanLabel($speed)
    {
        if ($speed >= 40) return 'Sangat Cepat';
        if ($speed >= 35) return 'Cepat';
        if ($speed >= 30) return 'Cukup Cepat';
        if ($speed >= 25) return 'Sedang';
        if ($speed >= 20) return 'Agak Lambat';
        return 'Lambat';
    }

    /**
     * Helper function untuk mendapatkan label ketelitian
     */
    private function getKetelitianLabel($percentage)
    {
        if ($percentage >= 90) return 'Sangat Teliti';
        if ($percentage >= 80) return 'Teliti';
        if ($percentage >= 70) return 'Cukup Teliti';
        if ($percentage >= 60) return 'Sedang';
        if ($percentage >= 50) return 'Kurang Teliti';
        return 'Tidak Teliti';
    }

    /**
     * Helper function untuk mendapatkan label konsentrasi
     */
    private function getKonsentrasiLabel($value)
    {
        if ($value >= 85) return 'Sangat Tinggi';
        if ($value >= 75) return 'Tinggi';
        if ($value >= 65) return 'Cukup';
        if ($value >= 55) return 'Sedang';
        if ($value >= 45) return 'Kurang';
        return 'Sangat Kurang';
    }

    /**
     * Helper function untuk mendapatkan label ketahanan
     */
    private function getKetahananLabel($value)
    {
        if ($value >= 85) return 'Sangat Baik';
        if ($value >= 75) return 'Baik';
        if ($value >= 65) return 'Cukup';
        if ($value >= 55) return 'Sedang';
        if ($value >= 45) return 'Kurang';
        return 'Sangat Kurang';
    }
}
