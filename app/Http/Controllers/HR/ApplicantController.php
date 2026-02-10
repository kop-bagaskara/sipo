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

class ApplicantController extends Controller
{
    /**
     * Display HRD dashboard.
     */
    public function dashboard()
    {
        $totalApplicants = Applicant::count();
        $newApplicantsThisMonth = Applicant::whereMonth('tanggal_melamar', now()->month)
            ->whereYear('tanggal_melamar', now()->year)
            ->count();
        $applicantsInProcess = Applicant::whereIn('status', ['test', 'interview'])->count();
        $completedTests = ApplicantTestResult::count();

        $recentApplicants = Applicant::with('testResults')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('hr.applicants.dashboard', compact(
            'totalApplicants',
            'newApplicantsThisMonth',
            'applicantsInProcess',
            'completedTests',
            'recentApplicants'
        ));
    }

    /**
     * Display a listing of applicants.
     */
    public function index(Request $request)
    {
        // Query for Staff Level (status_staff IS NULL or status_staff != 'under_staff')
        $query = Applicant::with('testResults')
            ->where(function($q) {
                $q->whereNull('status_staff')
                  ->orWhere('status_staff', '!=', 'under_staff');
            });

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
        // Set pagination path untuk staff level
        $applicants->setPath(url('sipo/hr/applicants'));

        // Get data for under staff level (status_staff = 'under_staff')
        $queryUnderStaff = Applicant::with('testResults')
            ->where('status_staff', 'under_staff');

        // Apply same filters for under staff
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $queryUnderStaff->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('posisi_dilamar', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $queryUnderStaff->where('status', $request->status);
        }

        if ($request->has('posisi') && $request->posisi) {
            $queryUnderStaff->where('posisi_dilamar', 'like', "%{$request->posisi}%");
        }

        $applicantsUnderStaff = $queryUnderStaff->orderBy('created_at', 'desc')->paginate(15);
        // Set pagination path untuk under staff level
        $applicantsUnderStaff->setPath(url('sipo/hr/staff-applicants'));

        // Get filter options
        $statuses = ['pending', 'test', 'interview', 'accepted', 'rejected'];
        $positions = Applicant::distinct()->pluck('posisi_dilamar')->filter();

        return view('hr.applicants.index', compact('applicants', 'applicantsUnderStaff', 'statuses', 'positions'));
    }

    /**
     * Show the form for creating a new applicant.
     */
    public function create(Request $request)
    {
        $applicant = null;

        // If ID is provided in URL, load existing applicant data
        if ($request->has('id')) {
            $applicant = Applicant::find($request->id);
        }

        return view('hr.applicants.create', compact('applicant'));
    }

    /**
     * Store a newly created applicant.
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

        return redirect()->route('hr.applicants.index')
            ->with('success', $message);
    }

    /**
     * Save individual tab data
     */
    private function saveTab(Request $request)
    {
        try {
            Log::info('SaveTab called', [
                'current_tab' => $request->input('current_tab'),
                'applicant_id' => $request->input('applicant_id'),
                'all_data' => $request->all()
            ]);

            $currentTab = $request->input('current_tab');
            $applicantId = $request->input('applicant_id');
            $savedTabs = $request->input('saved_tabs', '');

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
            $data['updated_by'] = auth()->id();

            // Process data based on current tab
            $this->processTabData($currentTab, $data, $request);

            if ($applicantId) {
                // Update existing applicant
                $applicant = Applicant::find($applicantId);
                if (!$applicant) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data pelamar tidak ditemukan'
                    ], 404);
                }

                $applicant->update($data);
                $applicantId = $applicant->id;
            } else {
                // Create new applicant
                $data['status'] = 'draft';
                $data['tanggal_melamar'] = now();
                $data['created_by'] = auth()->id();

                $applicant = Applicant::create($data);
                $applicantId = $applicant->id;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data tab berhasil disimpan',
                'applicant_id' => $applicantId
            ]);

        } catch (\Exception $e) {
            Log::error('SaveTab error', [
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
     * Display the specified applicant.
     * This page is for candidates (only shows personal data, no test results)
     */
    public function show(Applicant $applicant)
    {
        // Don't load testResults - this page is for candidates, not HRD
        return view('hr.applicants.show', compact('applicant'));
    }

    /**
     * Show the form for editing the specified applicant.
     */
    public function edit(Applicant $applicant)
    {
        return view('hr.applicants.edit', compact('applicant'));
    }

    /**
     * Update the specified applicant.
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

        return redirect()->route('hr.applicants.show', $applicant)
            ->with('success', 'Data pelamar berhasil diperbarui');
    }

    /**
     * Remove the specified applicant.
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

        return redirect()->route('hr.applicants.index')
            ->with('success', 'Data pelamar berhasil dihapus');
    }

    /**
     * Display list of tests for applicant
     */
    public function listTests(Applicant $applicant)
    {
        $applicant->load('testResults');

        $testTypes = [
            'test_1' => 'Tes Matematika',
            // 'test_2' => 'Tes Krapelin', // Sementara dinonaktifkan - mencari formula penilaian
            'test_3' => 'Tes Buta Warna',
            'test_4' => 'Tes Kepribadian'
        ];

        return view('hr.applicants.list-tests', compact('applicant', 'testTypes'));
    }

    /**
     * Display test results for HRD (evaluation page)
     */
    public function testResults(Applicant $applicant)
    {
        $applicant->load(['testResults' => function($query) {
            $query->orderBy('test_type');
        }, 'testResults.hrdConfirmedBy']);

        $testTypes = [
            'test_1' => 'Tes Matematika',
            // 'test_2' => 'Tes Krapelin', // Sementara dinonaktifkan - mencari formula penilaian
            'test_3' => 'Tes Buta Warna',
            'test_4' => 'Tes Kepribadian'
        ];

        return view('hr.applicants.test-results', compact('applicant', 'testTypes'));
    }

    /**
     * Get test results as JSON (for AJAX)
     */
    public function getTestResultsJson(Applicant $applicant)
    {
        $applicant->load('testResults');

        $testResults = $applicant->testResults->map(function($result) {
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
     * Confirm test result by HRD
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
     * Generate PDF report for test results
     */
    public function generateTestReport(Applicant $applicant)
    {
        $applicant->load(['testResults' => function($query) {
            $query->orderBy('test_type');
        }, 'testResults.hrdConfirmedBy']);

        // dd($applicant);

        $testTypes = [
            'test_1' => 'Tes Matematika',
            // 'test_2' => 'Tes Krapelin', // Sementara dinonaktifkan - mencari formula penilaian
            'test_3' => 'Tes Buta Warna',
            'test_4' => 'Tes Kepribadian'
        ];

        // Calculate summary
        $totalTests = $applicant->testResults->count();
        $totalScore = $applicant->testResults->sum('score');
        $totalMaxScore = $applicant->testResults->sum('max_score');
        $averagePercentage = $totalTests > 0 ? ($applicant->testResults->sum('score') / $applicant->testResults->sum('max_score')) * 100 : 0;

        $pdf = Pdf::loadView('hr.applicants.test-report-pdf', compact(
            'applicant',
            'testTypes',
            'totalTests',
            'totalScore',
            'totalMaxScore',
            'averagePercentage'
        ));

        $filename = 'Test_Report_' . str_replace(' ', '_', $applicant->nama_lengkap) . '_' . date('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Start test for applicant
     */
    public function startTest(Applicant $applicant, $testType)
    {
        $testTypes = [
            'test_1' => 'Tes Matematika',
            // 'test_2' => 'Tes Krapelin', // Sementara dinonaktifkan - mencari formula penilaian
            'test_3' => 'Tes Buta Warna',
            'test_4' => 'Tes Kepribadian'
        ];

        if (!array_key_exists($testType, $testTypes)) {
            return redirect()->back()->with('error', 'Jenis test tidak valid');
        }

        // Check if test already exists - jika sudah ada, tampilkan hasilnya di test-results page
        $existingTest = $applicant->testResults()->where('test_type', $testType)->first();
        if ($existingTest) {
            return redirect()->route('hr.applicants.test-results', $applicant)
                ->with('info', 'Test sudah pernah dikerjakan. Lihat hasil test di halaman evaluasi HRD.');
        }

        // Untuk test matematika, ambil soal dari database
        $mathQuestions = [];
        if ($testType == 'test_1') {
            // Ambil semua soal aktif dari master data
            $questions = MathQuestion::active()
                ->ordered()
                ->get();

            // Format data sesuai kebutuhan JavaScript
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

        // Untuk test buta warna, ambil random plates dari database (master data)
        $ishiharaPlates = [];
        if ($testType == 'test_3') {
            // Ambil random plates aktif dari master data (maksimal 20, atau sesuai yang ada)
            $plates = IshiharaPlate::active()
                ->inRandomOrder()
                ->limit(20)
                ->get();

            // Format data sesuai kebutuhan JavaScript
            foreach ($plates as $plate) {
                $ishiharaPlates[] = [
                    'id' => $plate->id,
                    'plate_number' => $plate->plate_number,
                    'image_path' => $plate->image_path, // Path dari master data
                    'correct' => $plate->correct_answer, // Correct answer dari master data
                    'number' => $plate->correct_answer, // Untuk display
                ];
            }
        }

        return view('hr.applicants.test', compact('applicant', 'testType', 'testTypes', 'ishiharaPlates', 'mathQuestions'));
    }

    /**
     * Submit test result
     */
    public function submitTest(Request $request, Applicant $applicant, $testType)
    {
        try {
            // For iframe-based test (like color blindness test), answers might be a JSON string
            $answers = $request->answers;
            if (is_string($answers)) {
                $answers = json_decode($answers, true);
            }

            // Validation rules - untuk test_4 (kepribadian) perlu screenshot
            $validationRules = [
                'answers' => 'required',
                'score' => 'required|numeric|min:0',
                'max_score' => 'required|numeric|min:1',
                'duration_minutes' => 'required|numeric|min:1',
                'notes' => 'nullable|string'
            ];

            // Untuk test kepribadian, screenshot wajib
            if ($testType == 'test_4') {
                $validationRules['test_screenshot'] = 'required|image|mimes:jpeg,png,jpg,gif|max:5120'; // Max 5MB
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                // Return JSON response for AJAX requests
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

            // Generate notes with classification for Ishihara test, Math test, Krapelin test, and Personality test
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
            } elseif ($testType == 'test_2') {
                // Klasifikasi hasil test Krapelin
                $classification = $this->classifyKraepelinTest($request->score, $request->max_score, $request->duration_minutes);
                if ($classification) {
                    $classificationNote = "\n\n[ANALISA HASIL TEST]\n";
                    $classificationNote .= "Kategori: {$classification['indonesian']}\n";
                    $classificationNote .= "Grade: {$classification['grade']}\n";
                    $classificationNote .= "Deskripsi: {$classification['description']}\n";

                    // Tambahkan aspects jika ada
                    if (isset($classification['aspects'])) {
                        $classificationNote .= "\n[ASPEK PENILAIAN]\n";
                        foreach ($classification['aspects'] as $key => $aspect) {
                            $aspectName = ucfirst($key);
                            if ($key == 'kelincahan') {
                                $classificationNote .= "{$aspectName}: {$aspect['value']} soal/menit ({$aspect['label']})\n";
                            } else {
                                $classificationNote .= "{$aspectName}: {$aspect['value']}% ({$aspect['label']})\n";
                            }
                        }
                    }

                    $classificationNote .= "\nRekomendasi: {$classification['recommendation']}";

                    $notes = ($notes ? $notes . "\n" : '') . $classificationNote;
                }
            } elseif ($testType == 'test_4') {
                // Tes Kepribadian - hasil dikirim via email dari fotokarakter.com
                $personalityNote = "\n\n[INFORMASI TES KEPRIBADIAN]\n";
                $personalityNote .= "Sumber Tes: fotokarakter.com\n";
                $personalityNote .= "Status: Selesai\n";
                $personalityNote .= "Catatan: Hasil tes kepribadian dikirim via email kepada peserta. Screenshot hasil tes telah diupload.\n";
                $personalityNote .= "Rekomendasi: Evaluasi hasil tes kepribadian dari screenshot dan email peserta untuk proses rekrutmen selanjutnya.";

                $notes = ($notes ? $notes . "\n" : '') . $personalityNote;
            }

            // Untuk test kepribadian, handle screenshot upload dan simpan screenshot_path di kolom terpisah
            $screenshotPath = null;
            if ($testType == 'test_4' && $request->hasFile('test_screenshot')) {
                $screenshot = $request->file('test_screenshot');
                $fileName = 'personality_test_' . $applicant->id . '_' . time() . '.' . $screenshot->getClientOriginalExtension();
                $screenshotPath = $screenshot->storeAs('test_results/personality', $fileName, 'public');

                // Update answers data dengan screenshot path (untuk backward compatibility)
                $answersData['screenshot_path'] = $screenshotPath;
                $answersData['screenshot_url'] = asset('storage/' . $screenshotPath);

                // Update notes dengan informasi screenshot
                if ($screenshotPath) {
                    $notes .= "\nScreenshot Hasil Tes: Tersedia\n";
                    $notes .= "Path Screenshot: storage/{$screenshotPath}\n";
                }
            }

            $testResult = ApplicantTestResult::create([
                'applicant_id' => $applicant->id,
                'test_type' => $testType,
                'test_name' => $request->test_name,
                'score' => $request->score,
                'max_score' => $request->max_score,
                'answers' => $answersData,
                'screenshot_path' => $screenshotPath,
                'test_date' => now(),
                'duration_minutes' => $request->duration_minutes,
                'notes' => $notes,
                'created_by' => auth()->check() ? auth()->id() : null
            ]);

            // Update applicant status if all tests completed
            if ($applicant->isAllTestsCompleted()) {
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

            return redirect()->route('public.applicant.create', ['id' => $applicant->id])
                ->with('success', 'Hasil test berhasil disimpan');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error submitting test result: ' . $e->getMessage(), [
                'applicant_id' => $applicant->id,
                'test_type' => $testType,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON response for AJAX requests
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
     * Menyelesaikan proses interview test
     * Mengupdate status applicant menjadi 'interview' setelah semua test selesai
     */
    public function finishTestProcess(Request $request, Applicant $applicant)
    {
        try {
            // Check apakah semua test sudah selesai
            if (!$applicant->isAllTestsCompleted()) {
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
     * Menampilkan halaman test completed
     */
    public function testCompleted(Applicant $applicant)
    {
        // Load test results untuk menampilkan summary
        $applicant->load('testResults');

        $testTypes = [
            'test_1' => 'Tes Matematika',
            // 'test_2' => 'Tes Krapelin', // Sementara dinonaktifkan - mencari formula penilaian
            'test_3' => 'Tes Buta Warna',
            'test_4' => 'Tes Kepribadian'
        ];

        return view('hr.applicants.test-completed', compact('applicant', 'testTypes'));
    }

    /**
     * Klasifikasi hasil test Ishihara berdasarkan standar medis
     * Referensi: https://www.challengetb.org/publications/tools/country/Ishihara_Tests.pdf
     *
     * @param int $correctAnswers
     * @param int $totalPlates
     * @return array|null
     */
    private function classifyIshiharaTest($correctAnswers, $totalPlates)
    {
        $percentage = $totalPlates > 0 ? round(($correctAnswers / $totalPlates) * 100, 2) : 0;

        // Algoritma klasifikasi berdasarkan jumlah jawaban benar
        // Untuk test 17-25 plates (standar Ishihara)
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

    /**
     * Klasifikasi hasil test Matematika berdasarkan skor
     *
     * @param int $score
     * @param int $maxScore
     * @return array|null
     */
    private function classifyMathTest($score, $maxScore)
    {
        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;

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
     * Klasifikasi hasil test Krapelin berdasarkan skor dan kecepatan
     * Test Krapelin mengukur: ketelitian, konsentrasi, ketahanan bekerja di bawah tekanan waktu
     *
     * @param int $score
     * @param int $maxScore
     * @param int $durationMinutes
     * @return array|null
     */
    private function classifyKraepelinTest($score, $maxScore, $durationMinutes)
    {
        $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;

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
