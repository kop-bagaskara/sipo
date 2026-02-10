<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantTestResult;
use App\Models\IshiharaPlate;
use App\Models\MathQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class StaffApplicantController extends Controller
{
    /**
     * Display a listing of staff-level applicants.
     */
    public function index(Request $request)
    {
        // Query for Under Staff Level (status_staff = 'under_staff')
        $query = Applicant::with('testResults')
            ->where('status_staff', 'under_staff');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('posisi_dilamar', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by position
        if ($request->has('posisi') && $request->posisi) {
            $query->where('posisi_dilamar', 'like', "%{$request->posisi}%");
        }

        $applicants = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $statuses = ['pending', 'test', 'interview', 'accepted', 'rejected'];
        $positions = Applicant::distinct()->pluck('posisi_dilamar')->filter();

        return view('hr.applicants-u-staff.index', compact('applicants', 'statuses', 'positions'));
    }

    /**
     * Show the form for creating a new staff-level applicant.
     */
    public function create(Request $request)
    {
        $applicant = null;

        // If ID is provided in URL, load existing applicant data
        if ($request->has('id')) {
            $applicant = Applicant::find($request->id);
        }

        return view('hr.applicants-u-staff.create', compact('applicant'));
    }

    /**
     * Store a newly created staff-level applicant.
     */
    public function store(Request $request)
    {
        // Check if this is a tab save request
        if ($request->has('current_tab')) {
            return $this->saveTab($request);
        }

        $validator = Validator::make($request->all(), [
            // Required fields
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:applicants,email',
            'no_handphone' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'posisi_dilamar' => 'required|string|max:255',

            // Optional fields
            'alias' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'agama' => 'nullable|string|max:50',
            'kebangsaan' => 'nullable|string|max:50',
            'no_ktp' => 'nullable|string|max:20',
            'alamat_ktp' => 'nullable|string',
            'kode_pos_ktp' => 'nullable|string|max:10',
            'alamat_domisili' => 'nullable|string',
            'kode_pos_domisili' => 'nullable|string|max:10',
            'no_npwp' => 'nullable|string|max:20',
            'bpjs_kesehatan' => 'nullable|string|max:20',
            'kontak_darurat' => 'nullable|string|max:20',
            'hubungan_kontak_darurat' => 'nullable|string|max:50',
            'gaji_terakhir' => 'nullable|string|max:255',
            'mulai_kerja' => 'nullable|date',
            'hobby' => 'nullable|string|max:255',
            'lain_lain' => 'nullable|string|max:255',
            'tanggal_deklarasi' => 'nullable|date',
            'ttd_pelamar' => 'nullable|string|max:255',

            // File uploads
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['cv_file', 'foto']);
        $data['tanggal_melamar'] = now();
        $data['status'] = 'pending';
        $data['created_by'] = auth()->id();
        $data['is_draft'] = $request->has('is_draft') ? true : false;

        // Process array data
        $data['pendidikan'] = $this->processArrayData($request->input('pendidikan', []));
        $data['kursus'] = $this->processArrayData($request->input('kursus', []));
        $data['pengalaman'] = $this->processArrayData($request->input('pengalaman', []));
        $data['keluarga_anak'] = $this->processArrayData($request->input('keluarga_anak', []));
        $data['keluarga_ortu'] = $this->processArrayData($request->input('keluarga_ortu', []));
        $data['referensi'] = $this->processArrayData($request->input('referensi', []));

        // Process language skills
        $data['bahasa'] = $request->input('bahasa', []);

        // Process SIM array
        $data['sim'] = $request->input('sim', []);

        // Handle file uploads
        if ($request->hasFile('cv_file')) {
            $data['cv_file'] = $request->file('cv_file')->store('applicants/cv', 'public');
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('applicants/photos', 'public');
        }

        $applicant = Applicant::create($data);

        $message = $data['is_draft'] ? 'Data pelamar berhasil disimpan sebagai draft.' : 'Data pelamar berhasil ditambahkan.';

        return redirect()->route('public.staff-applicant.index')
            ->with('success', $message);
    }

    /**
     * Save individual tab data
     */
    private function saveTab(Request $request)
    {
        // dd($request->all());
        try {
            Log::info('SaveTab called (Staff)', [
                'current_tab' => $request->input('current_tab'),
                'applicant_id' => $request->input('applicant_id'),
                'all_data' => $request->all()
            ]);

            $currentTab = $request->input('current_tab');
            $applicantId = $request->input('applicant_id');
            $savedTabs = $request->input('saved_tabs', '');

            $data['status_staff'] = 'under_staff';

            // Validation rules for each tab
            $validationRules = $this->getTabValidationRules($currentTab);

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->all();

            // Process data based on current tab
            $this->processTabData($currentTab, $data, $request);

            if ($applicantId) {
                // Update existing applicant
                $applicant = Applicant::find($applicantId);
                if (!$applicant) {
                    // If applicant_id not found, create new applicant instead
                    Log::warning('Applicant ID not found, creating new applicant (Staff)', [
                        'applicant_id' => $applicantId
                    ]);
                    $data['status'] = 'draft';
                    $data['tanggal_melamar'] = now();
                    $data['created_by'] = auth()->id();
                    $data['status_staff'] = 'under_staff';
                    // Don't set updated_by for new records

                    $applicant = Applicant::create($data);
                    $applicantId = $applicant->id;
                } else {
                    // Set updated_by only for updates
                    $data['updated_by'] = auth()->id();
                    $applicant->update($data);
                    $applicantId = $applicant->id;
                }
            } else {
                // Create new applicant
                $data['status'] = 'draft';
                $data['tanggal_melamar'] = now();
                $data['created_by'] = auth()->id();
                $data['status_staff'] = 'under_staff';
                // Don't set updated_by for new records

                $applicant = Applicant::create($data);
                $applicantId = $applicant->id;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data tab berhasil disimpan',
                'applicant_id' => $applicantId
            ]);

        } catch (\Exception $e) {
            Log::error('SaveTab error (Staff)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTabValidationRules($tab)
    {
        $rules = [];

        switch ($tab) {
            case 'posisi':
                $rules = [
                    'posisi_dilamar' => 'required|string|max:255',
                ];
                break;
            case 'data-diri':
                $applicantId = request()->input('applicant_id');
                $emailRule = Rule::unique('pgsql2.tb_applicants', 'email');
                if ($applicantId) {
                    $emailRule = $emailRule->ignore($applicantId, 'id');
                }

                $rules = [
                    'nama_lengkap' => 'required|string|max:255',
                    'jenis_kelamin' => 'required|in:L,P',
                    'tanggal_lahir' => 'required|date',
                    'no_handphone' => 'required|string|max:20',
                    'email' => ['required', 'email', 'max:255', $emailRule],
                ];
                break;
            case 'pendidikan':
            case 'kursus':
                // These tabs don't have required fields
                break;
            case 'pengalaman':
                // Validasi untuk pengalaman kerja - minimal 1 baris dengan field required
                $pengalamanData = request()->input('pengalaman', []);
                if (!empty($pengalamanData) && is_array($pengalamanData)) {
                    foreach ($pengalamanData as $index => $item) {
                        if (is_array($item)) {
                            // Cek apakah ada minimal satu field yang diisi
                            $hasAnyValue = !empty($item['nama_perusahaan']) ||
                                         !empty($item['tanggal_masuk']) ||
                                         !empty($item['tanggal_keluar']) ||
                                         !empty($item['bagian']) ||
                                         !empty($item['jabatan']);

                            if ($hasAnyValue) {
                                // Jika ada data, validasi field required
                                $rules["pengalaman.{$index}.nama_perusahaan"] = 'required|string|max:255';
                                $rules["pengalaman.{$index}.tanggal_masuk"] = 'required|date';
                                $rules["pengalaman.{$index}.tanggal_keluar"] = 'required|date|after_or_equal:pengalaman.' . $index . '.tanggal_masuk';
                                $rules["pengalaman.{$index}.bagian"] = 'required|string|max:255';
                                $rules["pengalaman.{$index}.jabatan"] = 'required|string|max:255';
                            }
                        }
                    }
                }
                break;
            case 'keluarga':
            case 'kemampuan':
                // These tabs don't have required fields
                break;
        }

        return $rules;
    }

    private function processTabData($tab, &$data, $request)
    {
        switch ($tab) {
            case 'pendidikan':
                $data['pendidikan'] = $this->processArrayData($request->input('pendidikan', []));
                break;
            case 'kursus':
                $data['kursus'] = $this->processArrayData($request->input('kursus', []));
                break;
            case 'pengalaman':
                $data['pengalaman'] = $this->processArrayData($request->input('pengalaman', []));
                break;
            case 'keluarga':
                $data['keluarga_anak'] = $this->processArrayData($request->input('keluarga_anak', []));
                $data['keluarga_ortu'] = $this->processArrayData($request->input('keluarga_ortu', []));
                break;
            case 'kemampuan':
                $data['bahasa'] = $request->input('bahasa', []);
                $data['sim'] = $request->input('sim', []);
                $data['referensi'] = $this->processArrayData($request->input('referensi', []));

                // Handle file uploads
                if ($request->hasFile('cv_file')) {
                    $data['cv_file'] = $request->file('cv_file')->store('applicants/cv', 'public');
                }

                if ($request->hasFile('foto')) {
                    $data['foto'] = $request->file('foto')->store('applicants/photos', 'public');
                }
                break;
        }
    }

    /**
     * Process array data to remove empty entries
     */
    private function processArrayData($arrayData)
    {
        if (!is_array($arrayData)) {
            return [];
        }

        $processed = [];
        foreach ($arrayData as $item) {
            if (is_array($item)) {
                // Check if any field has value
                $hasValue = false;
                foreach ($item as $value) {
                    if (!empty($value)) {
                        $hasValue = true;
                        break;
                    }
                }
                if ($hasValue) {
                    $processed[] = $item;
                }
            }
        }

        return $processed;
    }

    /**
     * Display the specified staff-level applicant.
     */
    public function show(Applicant $applicant)
    {
        return view('hr.applicants-u-staff.show', compact('applicant'));
    }

    /**
     * Show the form for editing the specified staff-level applicant.
     */
    public function edit(Applicant $applicant)
    {
        return view('hr.applicants-u-staff.edit', compact('applicant'));
    }

    /**
     * Update the specified staff-level applicant.
     */
    public function update(Request $request, Applicant $applicant)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('pgsql2.tb_applicants', 'email')->ignore($applicant->id, 'id')],
            'no_handphone' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'posisi_dilamar' => 'required|string|max:255',
            'tanggal_deklarasi' => 'nullable|date',
            'ttd_pelamar' => 'nullable|string|max:255',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['cv_file', 'foto', 'ttd_signature_image']);
        $data['updated_by'] = auth()->id();

        // Handle signature image
        if ($request->has('ttd_signature_image') && $request->ttd_signature_image) {
            $signatureData = $request->ttd_signature_image;

            // Check if it's a base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $signatureData, $type)) {
                $signatureData = substr($signatureData, strpos($signatureData, ',') + 1);
                $type = strtolower($type[1]);

                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    $type = 'png';
                }

                $signatureData = base64_decode($signatureData);

                if ($signatureData !== false) {
                    $fileName = 'signature_' . $applicant->id . '_' . time() . '.' . $type;
                    $filePath = 'applicants/signatures/' . $fileName;

                    // Delete old signature if exists
                    if ($applicant->ttd_signature) {
                        Storage::disk('public')->delete($applicant->ttd_signature);
                    }

                    // Save new signature
                    Storage::disk('public')->put($filePath, $signatureData);
                    $data['ttd_signature'] = $filePath;
                }
            }
        }

        // Handle file uploads
        if ($request->hasFile('cv_file')) {
            // Delete old file
            if ($applicant->cv_file) {
                Storage::disk('public')->delete($applicant->cv_file);
            }
            $data['cv_file'] = $request->file('cv_file')->store('applicants/cv', 'public');
        }

        if ($request->hasFile('foto')) {
            // Delete old file
            if ($applicant->foto) {
                Storage::disk('public')->delete($applicant->foto);
            }
            $data['foto'] = $request->file('foto')->store('applicants/photos', 'public');
        }

        $applicant->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelamar berhasil diperbarui',
                'applicant_id' => $applicant->id
            ]);
        }

        return redirect()->route('public.staff-applicant.show', $applicant)
            ->with('success', 'Data pelamar berhasil diperbarui');
    }

    /**
     * Remove the specified staff-level applicant.
     */
    public function destroy(Applicant $applicant)
    {
        // Delete files
        if ($applicant->cv_file) {
            Storage::disk('public')->delete($applicant->cv_file);
        }
        if ($applicant->foto) {
            Storage::disk('public')->delete($applicant->foto);
        }

        $applicant->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pelamar berhasil dihapus'
            ]);
        }

        return redirect()->route('public.staff-applicant.index')
            ->with('success', 'Data pelamar berhasil dihapus');
    }

    /**
     * Check if all staff-level tests are completed (only 2 tests: test_1 and test_3)
     */
    private function isAllStaffTestsCompleted(Applicant $applicant)
    {
        $completedTests = $applicant->testResults()
            ->whereIn('test_type', ['test_1', 'test_3'])
            ->count();

        return $completedTests >= 2;
    }

    /**
     * Display list of tests for staff-level applicant (only 2 tests)
     */
    public function listTests(Applicant $applicant)
    {
        $applicant->load('testResults');

        // Only 2 tests for staff level: test_1 (Matematika) and test_3 (Buta Warna)
        $testTypes = [
            'test_1' => 'Tes Matematika',
            'test_3' => 'Tes Buta Warna'
        ];

        return view('hr.applicants-u-staff.list-tests', compact('applicant', 'testTypes'));
    }

    /**
     * Display test results for HRD (evaluation page) - staff level
     */
    public function testResults(Applicant $applicant)
    {
        $applicant->load(['testResults' => function($query) {
            $query->orderBy('test_type');
        }, 'testResults.hrdConfirmedBy']);

        // Only 2 tests for staff level
        $testTypes = [
            'test_1' => 'Tes Matematika',
            'test_3' => 'Tes Buta Warna'
        ];

        return view('hr.applicants-u-staff.test-results', compact('applicant', 'testTypes'));
    }

    /**
     * Get test results as JSON (for AJAX) - staff level
     */
    public function getTestResultsJson(Applicant $applicant)
    {
        $applicant->load('testResults');

        // Filter only test_1 and test_3
        $testResults = $applicant->testResults()
            ->whereIn('test_type', ['test_1', 'test_3'])
            ->get()
            ->map(function($result) {
                return [
                    'test_type' => $result->test_type,
                    'test_name' => $result->test_name,
                    'score' => $result->score,
                    'max_score' => $result->max_score,
                    'test_date' => $result->test_date ? $result->test_date->format('Y-m-d H:i:s') : null,
                    'hrd_status' => $result->hrd_status,
                ];
            });

        return response()->json([
            'success' => true,
            'testResults' => $testResults,
            'applicant_status' => $applicant->status
        ]);
    }

    /**
     * Confirm test result by HRD - staff level
     */
    public function confirmTestResult(Request $request, Applicant $applicant, $testResultId)
    {
        $validator = Validator::make($request->all(), [
            'hrd_status' => 'required|in:approved,rejected',
            'hrd_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $testResult = ApplicantTestResult::where('id', $testResultId)
            ->where('applicant_id', $applicant->id)
            ->whereIn('test_type', ['test_1', 'test_3']) // Only allow test_1 and test_3
            ->firstOrFail();

        $testResult->update([
            'hrd_status' => $request->hrd_status,
            'hrd_notes' => $request->hrd_notes,
            'hrd_confirmed_by' => auth()->id(),
            'hrd_confirmed_at' => now()
        ]);

        $statusText = $request->hrd_status == 'approved' ? 'diterima' : 'ditolak';
        return redirect()->back()
            ->with('success', "Hasil test {$testResult->test_name_formatted} telah dikonfirmasi: {$statusText}");
    }

    /**
     * Generate PDF report for test results - staff level
     */
    public function generateTestReport(Applicant $applicant)
    {
        $applicant->load(['testResults' => function($query) {
            $query->orderBy('test_type');
        }, 'testResults.hrdConfirmedBy']);

        // Only 2 tests for staff level
        $testTypes = [
            'test_1' => 'Tes Matematika',
            'test_3' => 'Tes Buta Warna'
        ];

        // Filter only test_1 and test_3
        $filteredTestResults = $applicant->testResults()
            ->whereIn('test_type', ['test_1', 'test_3'])
            ->get();

        // Calculate summary
        $totalTests = $filteredTestResults->count();
        $totalScore = $filteredTestResults->sum('score');
        $totalMaxScore = $filteredTestResults->sum('max_score');
        $averagePercentage = $totalTests > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;

        $pdf = Pdf::loadView('hr.applicants-u-staff.test-report-pdf', compact(
            'applicant',
            'testTypes',
            'totalTests',
            'totalScore',
            'totalMaxScore',
            'averagePercentage',
            'filteredTestResults'
        ));

        $filename = 'Test_Report_Staff_' . str_replace(' ', '_', $applicant->nama_lengkap) . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Start test for staff-level applicant (only test_1 and test_3)
     */
    public function startTest(Applicant $applicant, $testType)
    {
        // Only allow test_1 and test_3
        if (!in_array($testType, ['test_1', 'test_3'])) {
            return redirect()->back()->with('error', 'Jenis test tidak valid untuk level staff');
        }

        $testTypes = [
            'test_1' => 'Tes Matematika',
            'test_3' => 'Tes Buta Warna'
        ];

        // Check if test already exists
        $existingTest = $applicant->testResults()->where('test_type', $testType)->first();
        if ($existingTest) {
            return redirect()->route('public.staff-applicant.test.results', $applicant)
                ->with('info', 'Test sudah pernah dikerjakan. Lihat hasil test di halaman evaluasi HRD.');
        }

        // Untuk test matematika, ambil soal dari database
        $mathQuestions = [];
        if ($testType == 'test_1') {
            $questions = MathQuestion::active()
                ->ordered()
                ->get();

            foreach ($questions as $question) {
                $mathQuestions[] = [
                    'id' => $question->id,
                    'question_number' => $question->question_number,
                    'question' => $question->question,
                    'correct_answer' => $question->answer,
                    'question_type' => $question->question_type,
                ];
            }
        }

        // Untuk test buta warna, ambil random plates dari database
        $ishiharaPlates = [];
        if ($testType == 'test_3') {
            $plates = IshiharaPlate::active()
                ->inRandomOrder()
                ->limit(20)
                ->get();

            foreach ($plates as $plate) {
                $ishiharaPlates[] = [
                    'id' => $plate->id,
                    'plate_number' => $plate->plate_number,
                    'image_path' => $plate->image_path,
                    'correct' => $plate->correct_answer,
                    'number' => $plate->correct_answer,
                ];
            }
        }

        return view('hr.applicants-u-staff.test', compact('applicant', 'testType', 'testTypes', 'ishiharaPlates', 'mathQuestions'));
    }

    /**
     * Submit test result - staff level (only test_1 and test_3)
     */
    public function submitTest(Request $request, Applicant $applicant, $testType)
    {
        try {
            // Only allow test_1 and test_3
            if (!in_array($testType, ['test_1', 'test_3'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis test tidak valid untuk level staff'
                ], 400);
            }

            // For iframe-based test, answers might be a JSON string
            $answers = $request->answers;
            if (is_string($answers)) {
                $answers = json_decode($answers, true);
            }

            // Validation rules
            $validationRules = [
                'answers' => 'required',
                'score' => 'required|numeric|min:0',
                'max_score' => 'required|numeric|min:1',
                'duration_minutes' => 'required|numeric|min:1',
                'notes' => 'nullable|string'
            ];

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Ensure answers is an array
            $answersData = is_array($answers) ? $answers : json_decode($answers, true);
            if (!is_array($answersData)) {
                $answersData = ['raw_data' => $answers];
            }

            // Generate notes with classification
            $notes = $request->notes ?? null;
            if ($testType == 'test_3') {
                // Klasifikasi hasil test Ishihara
                $classification = $this->classifyIshiharaTest($request->score, $request->max_score);
                if ($classification) {
                    $classificationNote = "\n\n[KLASIFIKASI HASIL TEST]\n";
                    $classificationNote .= "Kategori: {$classification['indonesian']}\n";
                    $classificationNote .= "Deskripsi: {$classification['description']}\n";
                    $classificationNote .= "Rekomendasi: {$classification['recommendation']}";

                    $notes = ($notes ? $notes . "\n" : '') . $classificationNote;
                }
            } elseif ($testType == 'test_1') {
                // Klasifikasi hasil test Matematika
                $classification = $this->classifyMathTest($request->score, $request->max_score);
                if ($classification) {
                    $classificationNote = "\n\n[ANALISA HASIL TEST]\n";
                    $classificationNote .= "Kategori: {$classification['indonesian']}\n";
                    $classificationNote .= "Grade: {$classification['grade']}\n";
                    $classificationNote .= "Deskripsi: {$classification['description']}\n";
                    $classificationNote .= "Rekomendasi: {$classification['recommendation']}";

                    $notes = ($notes ? $notes . "\n" : '') . $classificationNote;
                }
            }

            $testResult = ApplicantTestResult::create([
                'applicant_id' => $applicant->id,
                'test_type' => $testType,
                'test_name' => $request->test_name,
                'score' => $request->score,
                'max_score' => $request->max_score,
                'answers' => $answersData,
                'screenshot_path' => null,
                'test_date' => now(),
                'duration_minutes' => $request->duration_minutes,
                'notes' => $notes,
                'created_by' => auth()->check() ? auth()->id() : null
            ]);

            // Update applicant status if all staff tests completed (only 2 tests)
            if ($this->isAllStaffTestsCompleted($applicant)) {
                $applicant->update(['status' => 'interview']);
            } else {
                $applicant->update(['status' => 'test']);
            }

            // Return JSON response for AJAX requests, otherwise redirect
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hasil test berhasil disimpan',
                    'applicant_id' => $applicant->id
                ]);
            }

            return redirect()->route('public.staff-applicant.create', ['id' => $applicant->id])
                ->with('success', 'Hasil test berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Error submitting test result (Staff): ' . $e->getMessage(), [
                'applicant_id' => $applicant->id,
                'test_type' => $testType,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan hasil test: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan hasil test: ' . $e->getMessage());
        }
    }

    /**
     * Menyelesaikan proses interview test - staff level
     */
    public function finishTestProcess(Request $request, Applicant $applicant)
    {
        try {
            // Check apakah semua test sudah selesai (hanya 2 test untuk staff)
            if (!$this->isAllStaffTestsCompleted($applicant)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum semua test selesai dikerjakan. Silakan selesaikan semua test terlebih dahulu.'
                ], 400);
            }

            // Update status applicant menjadi 'interview'
            $applicant->update([
                'status' => 'interview'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proses interview test telah selesai. Status pelamar telah diupdate menjadi "Interview".'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman test completed - staff level
     */
    public function testCompleted(Applicant $applicant)
    {
        $applicant->load('testResults');

        // Only 2 tests for staff level
        $testTypes = [
            'test_1' => 'Tes Matematika',
            'test_3' => 'Tes Buta Warna'
        ];

        return view('hr.applicants-u-staff.test-completed', compact('applicant', 'testTypes'));
    }

    /**
     * Klasifikasi hasil test Ishihara
     */
    private function classifyIshiharaTest($correctAnswers, $totalPlates)
    {
        $percentage = $totalPlates > 0 ? round(($correctAnswers / $totalPlates) * 100, 2) : 0;

        if ($totalPlates >= 17 && $totalPlates <= 25) {
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

    /**
     * Klasifikasi hasil test Matematika
     */
    private function classifyMathTest($score, $maxScore)
    {
        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;

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
}

