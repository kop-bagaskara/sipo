<?php

use App\Http\Controllers\DataController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\ToolsControllerion;
use App\Models\Machine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MappingItemController;
use App\Http\Controllers\ProcessPrepressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevelopmentController;
use App\Http\Controllers\JobOrderController;
use App\Models\User;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\TrialSampleController;
use App\Http\Controllers\UserExecutionController;
use App\Http\Controllers\HRRequestController;
use App\Http\Controllers\HRDashboardController;
use App\Http\Controllers\HRApprovalController;
use App\Http\Controllers\EmployeeDataController;
use App\Http\Controllers\SupplierTicketController;
use App\Http\Controllers\AdminSupplierTicketController;
use App\Http\Controllers\GRDController;
use App\Http\Controllers\SecurityReportController;
use App\Http\Controllers\PaperProcurementController;
use Illuminate\Http\Request;
use App\Http\Controllers\EbookPKBController;
use App\Http\Controllers\ForecastingController;
use App\Http\Controllers\PortalTraining\PortalTrainingController;

// Public Barcode Leave Request Routes (No Auth Required)
Route::prefix('sipo/hr')->group(function () {
    Route::get('/leave-request/form', [App\Http\Controllers\HR\LeaveRequestController::class, 'showFormFromBarcode'])->name('public.leave-request.form');
    Route::get('/leave-request/employee-data/{id}', [App\Http\Controllers\HR\LeaveRequestController::class, 'getEmployeeData'])->name('public.leave-request.employee-data');
    Route::post('/leave-request', [App\Http\Controllers\HR\LeaveRequestController::class, 'store'])->name('public.leave-request.store');
    Route::get('/leave-request/success/{id}', [App\Http\Controllers\HR\LeaveRequestController::class, 'showSuccess'])->name('public.leave-request.success');
    Route::get('/leave-request/error', [App\Http\Controllers\HR\LeaveRequestController::class, 'showError'])->name('public.leave-request.error');


    Route::get('/applicants', [App\Http\Controllers\HR\ApplicantController::class, 'index'])->name('public.applicant.index');
    Route::get('/applicants/create', [App\Http\Controllers\HR\ApplicantController::class, 'create'])->name('public.applicant.create');
    Route::post('/applicants', [App\Http\Controllers\HR\ApplicantController::class, 'store'])->name('public.applicant.store');
    Route::get('/applicants/{applicant}', [App\Http\Controllers\HR\ApplicantController::class, 'show'])->name('public.applicant.show');
    Route::get('/applicants/{applicant}/edit', [App\Http\Controllers\HR\ApplicantController::class, 'edit'])->name('public.applicant.edit');
    Route::put('/applicants/{applicant}', [App\Http\Controllers\HR\ApplicantController::class, 'update'])->name('public.applicant.update');
    Route::delete('/applicants/{applicant}', [App\Http\Controllers\HR\ApplicantController::class, 'destroy'])->name('public.applicant.destroy');

    // test routes
    Route::get('/{applicant}/tests', [App\Http\Controllers\HR\ApplicantController::class, 'listTests'])->name('public.applicant.tests');
    Route::get('/{applicant}/test-results', [App\Http\Controllers\HR\ApplicantController::class, 'testResults'])->name('public.applicant.test.results');
    Route::get('/{applicant}/test-results-json', [App\Http\Controllers\HR\ApplicantController::class, 'getTestResultsJson'])->name('public.applicant.test.results-json');
    Route::post('/{applicant}/test-result/{testResult}/confirm', [App\Http\Controllers\HR\ApplicantController::class, 'confirmTestResult'])->name('public.applicant.test.result.confirm');
    Route::get('/{applicant}/test-report', [App\Http\Controllers\HR\ApplicantController::class, 'generateTestReport'])->name('public.applicant.test.report');
    Route::get('/{applicant}/test/{testType}', [App\Http\Controllers\HR\ApplicantController::class, 'startTest'])->name('public.applicant.test.start');
    Route::post('/{applicant}/test/{testType}', [App\Http\Controllers\HR\ApplicantController::class, 'submitTest'])->name('public.applicant.test.submit');
    Route::post('/{applicant}/finish-test-process', [App\Http\Controllers\HR\ApplicantController::class, 'finishTestProcess'])->name('public.applicant.finish-test-process');
    Route::get('/{applicant}/test-completed', [App\Http\Controllers\HR\ApplicantController::class, 'testCompleted'])->name('public.applicant.test.completed');

    // Staff-level applicants (only 2 tests: matematika & buta warna)
    Route::get('/staff-applicants', [App\Http\Controllers\HR\StaffApplicantController::class, 'index'])->name('public.staff-applicant.index');
    Route::get('/staff-applicants/create', [App\Http\Controllers\HR\StaffApplicantController::class, 'create'])->name('public.staff-applicant.create');
    Route::post('/staff-applicants', [App\Http\Controllers\HR\StaffApplicantController::class, 'store'])->name('public.staff-applicant.store');
    Route::get('/staff-applicants/{applicant}', [App\Http\Controllers\HR\StaffApplicantController::class, 'show'])->name('public.staff-applicant.show');
    Route::get('/staff-applicants/{applicant}/edit', [App\Http\Controllers\HR\StaffApplicantController::class, 'edit'])->name('public.staff-applicant.edit');
    Route::put('/staff-applicants/{applicant}', [App\Http\Controllers\HR\StaffApplicantController::class, 'update'])->name('public.staff-applicant.update');
    Route::delete('/staff-applicants/{applicant}', [App\Http\Controllers\HR\StaffApplicantController::class, 'destroy'])->name('public.staff-applicant.destroy');

    // Staff-level test routes (only test_1 and test_3)
    Route::get('/staff-applicants/{applicant}/tests', [App\Http\Controllers\HR\StaffApplicantController::class, 'listTests'])->name('public.staff-applicant.tests');
    Route::get('/staff-applicants/{applicant}/test-results', [App\Http\Controllers\HR\StaffApplicantController::class, 'testResults'])->name('public.staff-applicant.test.results');
    Route::get('/staff-applicants/{applicant}/test-results-json', [App\Http\Controllers\HR\StaffApplicantController::class, 'getTestResultsJson'])->name('public.staff-applicant.test.results-json');
    Route::post('/staff-applicants/{applicant}/test-result/{testResult}/confirm', [App\Http\Controllers\HR\StaffApplicantController::class, 'confirmTestResult'])->name('public.staff-applicant.test.result.confirm');
    Route::get('/staff-applicants/{applicant}/test-report', [App\Http\Controllers\HR\StaffApplicantController::class, 'generateTestReport'])->name('public.staff-applicant.test.report');
    Route::get('/staff-applicants/{applicant}/test/{testType}', [App\Http\Controllers\HR\StaffApplicantController::class, 'startTest'])->name('public.staff-applicant.test.start');
    Route::post('/staff-applicants/{applicant}/test/{testType}', [App\Http\Controllers\HR\StaffApplicantController::class, 'submitTest'])->name('public.staff-applicant.test.submit');
    Route::post('/staff-applicants/{applicant}/finish-test-process', [App\Http\Controllers\HR\StaffApplicantController::class, 'finishTestProcess'])->name('public.staff-applicant.finish-test-process');
    Route::get('/staff-applicants/{applicant}/test-completed', [App\Http\Controllers\HR\StaffApplicantController::class, 'testCompleted'])->name('public.staff-applicant.test.completed');
});


// Wrap semua routes dengan prefix 'sipo'
Route::group(['prefix' => 'sipo'], function () {


    Route::get('/', function () {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        } else {
            return view('auth.login');
        }
    });

    // Test SweetAlert
    Route::get('/test-sweetalert', function () {
        return view('test-sweetalert');
    });

    Route::get('/preview-reminder-notification', function () {
        // Dummy data for preview
        $user = (object) [
            'name' => 'John Doe'
        ];

        $job = (object) [
            'id' => 1,
            'job_code' => 'DEV-2024-001',
            'customer' => 'PT. Contoh Perusahaan',
            'product' => 'Website E-commerce',
            'status_job' => 'In Progress'
        ];

        $processName = 'Development';
        $daysBefore = 3; // Default H-3

        return view('emails.development.reminder-notification', compact('user', 'job', 'processName', 'daysBefore'));
    })->name('preview.reminder-notification');

    // Preview email prepress notification (template yang digunakan command development:send-prepress-reminders)
    Route::get('/preview-prepress-notification', function () {
        // Dummy data untuk preview prepress notification
        $setting = (object) [
            'process_name' => 'Assign ke Prepress',
            'description' => 'Assign ke Prepress dari RnD'
        ];

        $reminder = [
            'description' => 'Reminder H-2 - Job Prepress',
            'days' => '2'
        ];

        $jobData = [
            'id' => 1,
            'job_code' => 'DEV-250919-0002',
            'job_name' => '0 - PT. INOVASI TEKNOLOGI KOSMETIKA',
            'customer' => 'PT. INOVASI TEKNOLOGI KOSMETIKA',
            'product' => '0',
            'kode_design' => 'DES-001',
            'dimension' => '10x15 cm',
            'material' => 'Art Paper 210gsm',
            'total_color' => '4+0',
            'qty_order_estimation' => 1000,
            'job_type' => 'new',
            'prioritas_job' => 'Normal',
            'tanggal' => '2024-09-19',
            'prepress_deadline' => \Carbon\Carbon::now()->addDays(2),
            'catatan' => 'Job development untuk produk baru',
            'job_order' => [
                ['jenis_pekerjaan' => 'Design', 'unit_job' => '1'],
                ['jenis_pekerjaan' => 'Prepress', 'unit_job' => '1']
            ]
        ];

        $additionalData = [
            'notification_type' => 'prepress_reminder',
            'reminder_type' => 'H-2',
            'action_text' => 'Lihat Job Prepress',
            'action_url' => '#'
        ];

        $currentUser = (object) [
            'name' => 'Jalu Bagaskara',
            'email' => 'jalu@example.com'
        ];

        return view('emails.development-prepress-notification', compact('setting', 'reminder', 'jobData', 'additionalData', 'currentUser'));
    })->name('preview.prepress-notification');

    // Preview email dengan template baru (reminder-notification)
    Route::get('/preview-new-template', function () {
        // Dummy data untuk preview template baru dengan multiple jobs
        $user = (object) [
            'name' => 'Jalu Bagaskara'
        ];

        // Multiple jobs untuk simulasi
        $jobs = [
            (object) [
                'id' => 1,
                'job_code' => 'DEV-250919-0002',
                'customer' => 'PT. INOVASI TEKNOLOGI KOSMETIKA',
                'product' => 'Website E-commerce',
                'status_job' => 'IN_PROGRESS_PREPRESS'
            ],
            (object) [
                'id' => 2,
                'job_code' => 'DEV-250919-0003',
                'customer' => 'PT. CONTOH PERUSAHAAN',
                'product' => 'Mobile App',
                'status_job' => 'IN_PROGRESS_PREPRESS'
            ]
        ];

        $processName = 'Development';
        $daysBefore = 2; // H-2

        return view('emails.development.reminder-notification', compact('user', 'jobs', 'processName', 'daysBefore'));
    })->name('preview.new-template');

    // Dashboard Routes - Split by module for better performance
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/dashboard/ppic', [DashboardController::class, 'ppic'])->middleware(['auth', 'verified'])->name('dashboard.ppic');
    Route::get('/dashboard/prepress', [DashboardController::class, 'prepress'])->middleware(['auth', 'verified'])->name('dashboard.prepress');
    Route::get('/dashboard/development', [DashboardController::class, 'development'])->middleware(['auth', 'verified'])->name('dashboard.development');
    Route::get('/dashboard/security', [DashboardController::class, 'security'])->middleware(['auth', 'verified'])->name('dashboard.security');
    Route::get('/dashboard/supplier', [DashboardController::class, 'supplier'])->middleware(['auth', 'verified'])->name('dashboard.supplier');

    Route::get('/dashboard-karyawan', [DashboardController::class, 'dashboardKaryawan'])->middleware(['auth', 'verified'])->name('dashboard-karyawan');
    Route::get('/api/dashboard/prepress-data', [DashboardController::class, 'getPrepressData'])->middleware(['auth', 'verified'])->name('api.dashboard.prepress-data');
    // LAZY LOADING: AJAX endpoints untuk load data saat tab diklik
    Route::get('/api/dashboard/overview-data', [DashboardController::class, 'loadOverviewData'])->middleware(['auth', 'verified'])->name('api.dashboard.overview-data');
    Route::get('/api/dashboard/prepress-data-lazy', [DashboardController::class, 'loadPrepressData'])->middleware(['auth', 'verified'])->name('api.dashboard.prepress-data-lazy');

    // Test login route
    Route::get('/test-login', function () {
        $users = \App\Models\User::all(['id', 'name', 'email']);
        return response()->json([
            'users' => $users,
            'message' => 'Available users for testing'
        ]);
    })->name('test-login');

    Route::middleware(['auth', 'verified', 'keep.session'])->group(function () {
        // EBOOK PKB
        Route::get('ebook-pkb', [EbookPKBController::class, 'index'])->name('ebook-pkb.index');
        Route::get('ebook-pkb/search', [EbookPKBController::class, 'search'])->name('ebook-pkb.search');
        Route::get('ebook-pkb/logs', [EbookPKBController::class, 'logs'])->name('ebook-pkb.logs');
        Route::post('/ebook-pkb/tracking/update-progress', [EbookPKBController::class, 'trackProgress']);
        Route::post('/ebook-pkb/tracking/mark-complete', [EbookPKBController::class, 'markSessionComplete']);
        Route::get('/ebook-pkb/tracking/current-session', [EbookPKBController::class, 'getCurrentSession']);
        Route::get('/ebook-pkb/tracking/user-statistics', [EbookPKBController::class, 'getUserStatistics']);


        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Account Setting Routes
        Route::get('/account-setting', [App\Http\Controllers\AccountSettingController::class, 'index'])->name('account.setting');
        Route::patch('/account-setting/profile', [App\Http\Controllers\AccountSettingController::class, 'updateProfile'])->name('account.setting.profile');
        Route::patch('/account-setting/password', [App\Http\Controllers\AccountSettingController::class, 'updatePassword'])->name('account.setting.password');
        Route::patch('/account-setting/avatar', [App\Http\Controllers\AccountSettingController::class, 'updateAvatar'])->name('account.setting.avatar');

        Route::group(['prefix' => 'proses'], function () {
            Route::get('/', [ProcessController::class, 'indexProcess'])->name('proses.index');

            // Pilih proses (tampilan tabel proses)
            Route::get('planning', [ProcessController::class, 'pilihRencanaPlan'])->name('mulai-proses.plan');
            // Halaman planning (menampilkan SO setelah parameter processes dipilih)
            Route::get('planning/lanjut', function () {
                return view('main.process.planning');
            })->name('planning.blade');
            // Halaman planning GLUEING (terpisah dari planning umum)
            Route::get('planning/glueing', [ProcessController::class, 'planningGlueing'])->name('planning.glueing');
            Route::post('submit-plan-first', [ProcessController::class, 'submitPlanFirst'])->name('plan-first.submit');
            Route::post('submit-plan-glueing', [ProcessController::class, 'submitPlanGlueing'])->name('plan-glueing.submit');
            Route::post('save-plan-from-preview', [ProcessController::class, 'savePlanFromPreview'])->name('plan-first.save-from-preview');
            Route::get('ptg-tum-choice', [ProcessController::class, 'showPtgTumChoice'])->name('ptg-tum.choice');

            // Machine mapping dan capacity untuk planning
            Route::post('get-machine-mapping', [ProcessController::class, 'getMachineMapping'])->name('get-machine-mapping');
            Route::post('get-machine-capacity', [ProcessController::class, 'getMachineCapacity'])->name('get-machine-capacity');
        });

        // Proses PLAN
        Route::get('rencana-planning', [ProcessController::class, 'indexRencanaPlan'])->name('rencana-plan.index');
        Route::get('wo-data', [ProcessController::class, 'indexDataWo'])->name('wo-data.index');
        Route::get('hasil-cetak', [ProcessController::class, 'indexDataHasilCetak'])->name('hasil-cetak.index');
        Route::get('plan-mingguan', [ProcessController::class, 'indexPlanMingguan'])->name('plan-mingguan.index');
        Route::get('get-data-plan-first/{code}', [ProcessController::class, 'getDataPlanFirst'])->name('data-plan-first.data');
        Route::get('get-data-plans-first/{code}', [ProcessController::class, 'getDataFirstPlansData'])->name('data-plans-first.data');
        Route::post('get-data-pelumasanmtc', [ProcessController::class, 'dataPelumasanMaintenance'])->name('data.pelumasan-maintenance');
        Route::get('get-data-pelumasanmtc-tb', [ProcessController::class, 'dataPelumasanMaintenanceTb'])->name('data.pelumasan-maintenance-tn');

        // Maintenance routes for timeline table
        Route::get('maintenance/lubrication-timeline', [App\Http\Controllers\MaintenanceController::class, 'getLubricationForTimeline'])->name('maintenance.lubrication-timeline');
        Route::get('maintenance/lubrication-machine/{machineCode}', [App\Http\Controllers\MaintenanceController::class, 'getLubricationByMachine'])->name('maintenance.lubrication-machine');
        Route::post('save-priority-changes', [ProcessController::class, 'savePriorityChanges'])->name('save-priority-changes');

        // DATA
        Route::get('/data-plan-first', [DataController::class, 'indexFirstPlanDataResult'])->name('plan-first.data');
        Route::post('/data-plan-firsts', [DataController::class, 'indexFirstPlanDataResults'])->name('plan-firsts.data');

        Route::get('/data-plan-harian', [DataController::class, 'indexPlanHarianDataResult'])->name('plan-harian.data');
        Route::post('/data-plan-harians', [DataController::class, 'indexPlanHarianDataResults'])->name('plan-harians.data');

        // Plan Harian Routes
        Route::get('/plan-harians', [DataController::class, 'indexPlanHarianDataResult'])->name('plan-harians.index');
        Route::post('/plan-harians/data', [DataController::class, 'indexPlanHarianDataResults'])->name('plan-harians.data');
        Route::post('/plan-harians/change-date', [DataController::class, 'changePlanDate'])->name('plan-harians.change-date');
        Route::post('/plan-harians/mark-urgent', [DataController::class, 'markPlanUrgent'])->name('plan-harians.mark-urgent');
        Route::post('/plan-harians/geser-plan', [DataController::class, 'geserPlan'])->name('plan-harians.geser-plan');

        // MASTER

        // Master Machine
        Route::get('master-machine', [MasterDataController::class, 'indexMachine'])->name('machine.index');
        Route::post('master-machine-data', [MasterDataController::class, 'machineIndexDataDetail'])->name('master.machine-data');
        Route::post('master-machine-store', [MasterDataController::class, 'machineStore'])->name('master.machine-store');
        Route::get('master-machine-data-sim', [MasterDataController::class, 'machineIndexDataDetailSim'])->name('master.machine-data-sim');

        // Master User
        Route::get('master-user', [MasterDataController::class, 'indexUser'])->name('user.index');
        Route::post('master-user-data', [MasterDataController::class, 'userIndexDataDetail'])->name('master.user-data');
        Route::post('submit-master-user-data', [MasterDataController::class, 'submitMasterUserData'])->name('master.user-data-submit');
        Route::get('master-user-data-detail/{id}', [MasterDataController::class, 'userIndexDataDetailData'])->name('master.user-data-detail');
        Route::post('delete-master-user-data/{id}', [MasterDataController::class, 'deleteMasterUserData'])->name('master.delete-master-user-data');

        // Master Divisi
        Route::get('master-divisi', [MasterDataController::class, 'indexDivisi'])->name('divisi.index');
        Route::post('master-divisi-data', [MasterDataController::class, 'divisiIndexDataDetail'])->name('master.divisi-data');
        Route::post('submit-master-divisi-data', [MasterDataController::class, 'submitMasterDivisiData'])->name('master.divisi-data-submit');
        Route::get('master-divisi-data-detail/{id}', [MasterDataController::class, 'divisiIndexDataDetailData'])->name('master.divisi-data-detail');
        Route::post('delete-master-divisi-data/{id}', [MasterDataController::class, 'deleteMasterDivisiData'])->name('master.delete-master-divisi-data');

        // Master Level
        Route::get('master-level', [MasterDataController::class, 'indexLevel'])->name('level.index');
        Route::post('master-level-data', [MasterDataController::class, 'levelIndexDataDetail'])->name('master.level-data');
        Route::post('submit-master-level-data', [MasterDataController::class, 'submitMasterLevelData'])->name('master.level-data-submit');
        Route::get('master-level-data-detail/{id}', [MasterDataController::class, 'levelIndexDataDetailData'])->name('master.level-data-detail');
        Route::post('delete-master-level-data/{id}', [MasterDataController::class, 'deleteMasterLevelData'])->name('master.delete-master-level-data');

        // Master Menu Navigation Settings
        Route::resource('menu-navigation-settings', \App\Http\Controllers\MenuNavigationSettingController::class)->names([
            'index' => 'master.menu-navigation-settings.index',
            'create' => 'master.menu-navigation-settings.create',
            'store' => 'master.menu-navigation-settings.store',
            'show' => 'master.menu-navigation-settings.show',
            'edit' => 'master.menu-navigation-settings.edit',
            'update' => 'master.menu-navigation-settings.update',
            'destroy' => 'master.menu-navigation-settings.destroy',
        ]);

        // Master Jabatan
        Route::get('master-jabatan', [MasterDataController::class, 'indexJabatan'])->name('jabatan.index');
        Route::post('master-jabatan-data', [MasterDataController::class, 'jabatanIndexDataDetail'])->name('master.jabatan-data');
        Route::post('submit-master-jabatan-data', [MasterDataController::class, 'submitMasterJabatanData'])->name('master.jabatan-data-submit');
        Route::get('master-jabatan-data-detail/{id}', [MasterDataController::class, 'jabatanIndexDataDetailData'])->name('master.jabatan-data-detail');
        Route::post('delete-master-jabatan-data/{id}', [MasterDataController::class, 'deleteMasterJabatanData'])->name('master.delete-master-jabatan-data');

        // Master Series Material
        Route::get('master-series-material', [MasterDataController::class, 'indexSeriesMaterial'])->name('series-material.index');
        Route::post('master-series-material-data', [MasterDataController::class, 'seriesMaterialIndexDataDetail'])->name('master.series-material-data');
        Route::post('submit-master-series-material-data', [MasterDataController::class, 'submitMasterSeriesMaterialData'])->name('master.series-material-data-submit');

        // Master Database Machine
        Route::get('master-database-machines', [MasterDataController::class, 'indexDatabaseMachines'])->name('database-machines.index');
        Route::post('master-database-machines-data', [MasterDataController::class, 'databaseMachinesIndexDataDetail'])->name('master.database-machines-data');
        Route::post('submit-master-database-machines-data', [MasterDataController::class, 'submitMasterDatabaseMachinesData'])->name('master.database-machines-data-submit');

        Route::get('mapping-item', [MasterDataController::class, 'indexMappingItem'])->name('mapping-item.index');
        Route::post('mapping-items/upload', [MasterDataController::class, 'uploadMappingItem'])->name('mapping-item.upload');

        Route::post('send-machine-joborder', [MasterDataController::class, 'sendMachineJobOrder'])->name('send-machine-joborder.data');

        // TOOLS
        Route::get('stock-transfer', [ToolsController::class, 'indexToolsStocTransfer'])->name('stc.tools');
        Route::post('import-stock-transfer', [ToolsController::class, 'filterAndStore'])->name('stc.import');
        Route::get('download-stock-transfer', [ToolsController::class, 'downloadFilteredFile'])->name('stc.download');

        Route::get('inventory-calc-stock', [ToolsController::class, 'indexToolsInvStock'])->name('inventory-calc-stock.index');

        Route::get('report-purchaseorder', [ToolsController::class, 'indexToolsPurchaseOrderReport'])->name('report-po.tools');
        Route::post('fetch-report', [ToolsController::class, 'fetchReportPO'])->name('report.fetch');

        Route::post('/save-plan-mingguan-data', [ProcessController::class, 'savePlanMingguanData'])->name('save-plan-mingguan.data');

        Route::post('/inventory/search', [InventoryController::class, 'search'])->name('inventory.search');
        Route::post('/salesorder/search', [InventoryController::class, 'SalesOrderController'])->name('salesorder.search');

        Route::post('/material/calculate', [InventoryController::class, 'calculateMaterial'])->name('material.calculate');

        Route::post('/material-needs/export', [InventoryController::class, 'exportMaterialNeeds'])->name('material.needs.export');

        Route::get('/planfistproduction/view', [ProcessController::class, 'viewPlanFistProduction'])->name('planfistproduction.view');

        Route::get('/plan-first-production', [ProcessController::class, 'viewPlanFirstProduction'])->name('plan.first.production');
        Route::get('/plan-first-production/data', [ProcessController::class, 'getPlanFirstData'])->name('plan.first.data');
        Route::get('/plan-plong/data', [ProcessController::class, 'getPlanPlongData'])->name('plan.plong.data');
        Route::get('/plan-glueing/data', [ProcessController::class, 'getPlanGlueingData'])->name('plan.glueing.data');
        Route::get('/plan-first-production/export', [ProcessController::class, 'exportPlanFirst'])->name('plan.first.export');
        Route::get('/plan-continued-production/data', [ProcessController::class, 'getPlanContinuedData'])->name('plan.continued.data');
        Route::get('/master-machine/data', [ProcessController::class, 'getMachineData'])->name('master.machine.data');
        Route::get('/master-machine/data-cetak', [ProcessController::class, 'getMachineDataCetak'])->name('master.machine-cetak.data');

        Route::get('departments', [ProcessController::class, 'getDepartments'])->name('departments.list');

        // Settings routes
        Route::get('settings/active-machines', [App\Http\Controllers\SettingController::class, 'getActiveMachines'])->name('settings.active-machines');
        Route::post('settings/active-machines', [App\Http\Controllers\SettingController::class, 'setActiveMachines'])->name('settings.set-active-machines');
        Route::get('settings/all-machines', [App\Http\Controllers\SettingController::class, 'getAllMachines'])->name('settings.all-machines');
        Route::post('settings/toggle-machine', [App\Http\Controllers\SettingController::class, 'toggleMachineActive'])->name('settings.toggle-machine');
        Route::resource('settings', App\Http\Controllers\SettingController::class);

        Route::get('/process/plan-first-prd', [ProcessController::class, 'indexPlanFirstProduction'])->name('process.plan-first-prd');
        Route::get('/process/plan-first-table', [ProcessController::class, 'indexPlanFirstTable'])->name('process.plan-first-table');
        Route::get('/process/plan-first-table-plong', [ProcessController::class, 'indexPlanFirstTablePlong'])->name('process.plan-first-table-plong');
        Route::get('/process/plan-first-table-uppic', [ProcessController::class, 'indexPlanFirstTableUppic'])->name('process.plan-first-table-uppic');
        Route::get('/process/plan-first-table-prepress', [ProcessController::class, 'indexPlanFirstTablePrepress'])->name('process.plan-first-table-prepress');
        Route::get('/process/plan-first-table-glueing', [ProcessController::class, 'indexPlanFirstTableGlueing'])->name('process.plan-first-table-glueing');
        Route::post('/prepress/save-plate', [ProcessController::class, 'savePlatePrepress'])->name('prepress.save-plate');
        Route::get('/process/search-materials', [ProcessController::class, 'searchMaterials'])->name('process.search-materials');
        Route::get('/process/fullcalendar-timeline', [ProcessController::class, 'indexFullCalendarTimeline'])->name('process.fullcalendar-timeline');
        Route::get('/process/fullcalendar-test', [ProcessController::class, 'indexFullCalendarTest'])->name('process.fullcalendar-test');
        Route::get('/process/dhtmlx-scheduler', [ProcessController::class, 'indexDhtmlxScheduler'])->name('process.dhtmlx-scheduler');
        Route::get('/process/custom-timeline', [ProcessController::class, 'indexCustomTimeline'])->name('process.custom-timeline');

        // BOM routes
        Route::get('/bom/details/{formula}', [ProcessController::class, 'getBomDetailsByFormula'])->name('bom.details');
        Route::get('/bom/{materialCode}/{process}', [ProcessController::class, 'getBomData'])->name('bom.get');

        // Mapping Item
        Route::post('mapping-items/data', [MappingItemController::class, 'data'])->name('mapping-items.data');
        Route::get('mapping-items/test', [MappingItemController::class, 'testData'])->name('mapping-items.test');
        Route::resource('mapping-items', MappingItemController::class);

        // Master Data Prepress
        Route::get('master-data-prepress', [MasterDataController::class, 'indexMasterDataPrepress'])->name('master-data-prepress.index');
        Route::post('master-data-prepress/data', [MasterDataController::class, 'masterDataPrepressIndexDataDetail'])->name('master-data-prepress.data');
        Route::post('master-data-prepress/submit', [MasterDataController::class, 'submitMasterDataPrepress'])->name('master-data-prepress.submit');
        Route::get('master-data-prepress/detail/{id}', [MasterDataController::class, 'masterDataPrepressDetail'])->name('master-data-prepress.detail');
        Route::post('master-data-prepress/update/{id}', [MasterDataController::class, 'updateMasterDataPrepress'])->name('master-data-prepress.update');

        // Master Working Days
        Route::get('master-working-days', [MasterDataController::class, 'indexWorkingDays'])->name('working-days.index');
        Route::post('master-working-days/data', [MasterDataController::class, 'workingDaysIndexDataDetail'])->name('master.working-days-data');
        Route::post('submit-master-working-days-data', [MasterDataController::class, 'submitMasterWorkingDaysData'])->name('master.working-days-submit');
        Route::get('master-working-days-data-detail/{id}', [MasterDataController::class, 'workingDaysIndexDataDetailData'])->name('master.working-days-data-detail');
        Route::post('delete-master-working-days-data/{id}', [MasterDataController::class, 'deleteMasterWorkingDaysData'])->name('master.delete-working-days-data');

        // Master Holiday Days
        Route::get('master-holiday-days', [MasterDataController::class, 'indexHolidayDays'])->name('holiday-days.index');
        Route::post('master-holiday-days-data', [MasterDataController::class, 'holidayDaysIndexDataDetail'])->name('master.holiday-days-data');
        Route::post('submit-master-holiday-days-data', [MasterDataController::class, 'submitMasterHolidayDaysData'])->name('master.holiday-days-submit');
        Route::get('master-holiday-days-data-detail/{id}', [MasterDataController::class, 'holidayDaysIndexDataDetailData'])->name('master.holiday-days-data-detail');
        Route::post('delete-master-holiday-days-data/{id}', [MasterDataController::class, 'deleteMasterHolidayDaysData'])->name('master.delete-holiday-days-data');
        Route::post('master-data-prepress/delete/{id}', [MasterDataController::class, 'deleteMasterDataPrepress'])->name('master-data-prepress.delete');

        // Job Order Prepress
        Route::get('prepress/job-order', [ProcessPrepressController::class, 'indexJobOrderPrepress'])->name('prepress.job-order.index');
        Route::get('prepress/job-order-new', [ProcessPrepressController::class, 'indexJobOrderPrepressNew'])->name('prepress.job-order.index-new');
        Route::get('prepress/job-order/next-number', [ProcessPrepressController::class, 'getNextJobOrderNumber'])->name('prepress.job-order.next-number');
        Route::post('prepress/job-order/submit', [ProcessPrepressController::class, 'submitJobOrderPrepress'])->name('prepress.job-order.submit');
        Route::post('prepress/job-order/submit-new', [ProcessPrepressController::class, 'submitJobOrderPrepressNew'])->name('prepress.job-order.submit-new');
        Route::post('prepress/job-order/check-limit', [ProcessPrepressController::class, 'checkJobOrderLimit'])->name('prepress.job-order.check-limit');
        Route::post('prepress/job-order-time/check-limit', [ProcessPrepressController::class, 'checkJobOrderLimitTime'])->name('prepress.job-order.check-limit-time');
        Route::post('prepress/job-order/get-unit-job', [ProcessPrepressController::class, 'getUnitJob'])->name('prepress.job-order.get-unit-job');
        Route::post('prepress/job-order/data', [ProcessPrepressController::class, 'jobOrderPrepressData'])->name('prepress.job-order.data');
        Route::post('prepress/job-order-plan/data', [ProcessPrepressController::class, 'jobOrderPrepressDataPlan'])->name('prepress.job-order-plan.data');
        Route::post('prepress/plan-selected/data', [ProcessPrepressController::class, 'jobOrderPrepressDataPlanSelected'])->name('prepress.plan-selected.data');
        Route::post('prepress/plan-selected/data-work-order', [ProcessPrepressController::class, 'jobOrderPrepressDataPlanSelectedWorkOrder'])->name('prepress.plan-selected.data-work-order');
        Route::post('prepress/plan-assigned/data', [ProcessPrepressController::class, 'jobOrderPrepressDataPlanAssigned'])->name('prepress.plan-assigned.data');
        Route::get('prepress/job-order-data', [ProcessPrepressController::class, 'indexJobOrderPrepressData'])->name('prepress.job-order.data.index');
        Route::get('prepress/job-order/detail/{id}', [ProcessPrepressController::class, 'jobOrderDetail'])->name('prepress.job-order.detail');
        Route::get('prepress/job-order-assign/detail/{id}', [ProcessPrepressController::class, 'jobOrderDetailAssign'])->name('prepress.job-order-assign.detail');
        Route::post('prepress/job-order/submit-plan', [ProcessPrepressController::class, 'submitJobOrderPrepressPlan'])->name('prepress.job-order.submit-plan');
        Route::post('prepress/job-order/export-plan', [ProcessPrepressController::class, 'exportJobOrderPrepressPlan'])->name('prepress.job-order.export-plan');

        // Jenis Pekerjaan Prepress Routes
        Route::get('jenis-pekerjaan-prepress', [App\Http\Controllers\JenisPekerjaanPrepressController::class, 'index'])->name('jenis-pekerjaan-prepress.index');
        Route::post('jenis-pekerjaan-prepress/data', [App\Http\Controllers\JenisPekerjaanPrepressController::class, 'data'])->name('jenis-pekerjaan-prepress.data');
        Route::post('jenis-pekerjaan-prepress/submit', [App\Http\Controllers\JenisPekerjaanPrepressController::class, 'submit'])->name('jenis-pekerjaan-prepress.submit');
        Route::get('jenis-pekerjaan-prepress/detail/{id}', [App\Http\Controllers\JenisPekerjaanPrepressController::class, 'detail'])->name('jenis-pekerjaan-prepress.detail');
        Route::post('jenis-pekerjaan-prepress/delete/{id}', [App\Http\Controllers\JenisPekerjaanPrepressController::class, 'delete'])->name('jenis-pekerjaan-prepress.delete');
        Route::get('jenis-pekerjaan-prepress/active', [App\Http\Controllers\JenisPekerjaanPrepressController::class, 'getActiveJenisPekerjaan'])->name('jenis-pekerjaan-prepress.active');
        Route::get('prepress/job-order/assign-job/{id}', [ProcessPrepressController::class, 'assignJobOrderPrepress'])->name('prepress.job-order.assign-job');
        Route::get('prepress/work-order/assign-job/{id}', [ProcessPrepressController::class, 'assignWorkOrderPrepress'])->name('prepress.work-order.assign-job');
        Route::get('prepress/job-order/assign-job-data/{id}', [ProcessPrepressController::class, 'assignJobOrderPrepressData'])->name('prepress.job-order.assign-job-data');
        Route::post('prepress/job-order/delete-plan/{id}', [ProcessPrepressController::class, 'deleteJobOrderPrepressPlan'])->name('prepress.job-order.delete-plan');
        Route::post('prepress/job-order/delete-attachment/{id}', [ProcessPrepressController::class, 'deleteAttachmentJobOrder'])->name('prepress.job-order.delete-attachment');

        // Job Order Status Check Routes
        Route::post('/job-orders/check-status', [JobOrderController::class, 'checkJobOrderStatus'])->name('job-orders.check-status');
        Route::post('/job-orders/check-status-by-items', [JobOrderController::class, 'checkJobOrderStatusByItems'])->name('job-orders.check-status-by-items');
        Route::get('/job-orders/details', [JobOrderController::class, 'getJobOrderDetails'])->name('job-orders.details');
        Route::get('/job-orders/missing-summary', [JobOrderController::class, 'getMissingJobOrdersSummary'])->name('job-orders.missing-summary');
        Route::post('/job-orders/get-by-wo-docno', [JobOrderController::class, 'getJobOrderByWODocNo'])->name('job-orders.get-by-wo-docno');
        Route::post('/job-orders/get-by-wo-docnos', [JobOrderController::class, 'getJobOrdersByWODocNos'])->name('job-orders.get-by-wo-docnos');
        Route::post('/job-orders/get-paper-size', [JobOrderController::class, 'getPaperSizeByMaterialCode'])->name('job-orders.get-paper-size');
        Route::post('/job-orders/get-paper-sizes', [JobOrderController::class, 'getPaperSizesByMaterialCodes'])->name('job-orders.get-paper-sizes');

        Route::get('prepress/plan-harian', [ProcessPrepressController::class, 'indexDataPlanHarianPPIC'])->name('prepress.planharian.index');
        Route::get('prepress/list-task', [ProcessPrepressController::class, 'indexDataListTask'])->name('prepress.listtask.index');
        Route::get('prepress/list-plan', [ProcessPrepressController::class, 'indexDataListPlan'])->name('prepress.listplan.index');
        Route::get('prepress/timeline-task', [ProcessPrepressController::class, 'indexDataTimelineTask'])->name('prepress.timelinetask.index');

        Route::post('prepress/job-order/delete-job-order-wo/{id}', [ProcessPrepressController::class, 'deleteJobOrderPrepressWO'])->name('prepress.job-order.delete-job-order-wo');

        Route::get('report-job-order-prepress', [ReportController::class, 'indexReportJobOrderPrepress'])->name('report.job-order-prepress.index');
        Route::get('report-plan-production', [ReportController::class, 'indexReportPlanProduction'])->name('report.plan-production.index');
        Route::post('report-plan-production/data', [ReportController::class, 'getReportDataPlanProduction'])->name('report.plan-production.data');
        Route::post('report-plan-production/export', [ReportController::class, 'exportReportPlanProduction'])->name('report.plan-production.export');
        // PROSES DATA
        Route::post('submit-progress-data', [ProcessPrepressController::class, 'submitProgressDataPrepress'])->name('progress-data-prepress.submit');
        Route::post('progress-data-prepress/pause', [ProcessPrepressController::class, 'pauseProgressDataPrepress'])->name('progress-data-prepress.pause');
        Route::post('progress-data-prepress/resume', [ProcessPrepressController::class, 'resumeProgressDataPrepress'])->name('progress-data-prepress.resume');
        Route::post('progress-data-prepress/finish', [ProcessPrepressController::class, 'finishProgressDataPrepress'])->name('progress-data-prepress.finish');
        Route::post('progress-data-prepress/save-catatan', [ProcessPrepressController::class, 'saveCatatanProgressDataPrepress'])->name('progress-data-prepress.save-catatan');

        Route::post('prepress/job-order/assign-user', [ProcessPrepressController::class, 'assignJobOrderPrepressUser'])->name('prepress.job-order.assign-user');

        Route::post('prepress/job-order/update/{id}', [ProcessPrepressController::class, 'updateJobOrderPrepress'])->name('prepress.job-order.update');
        Route::post('prepress/job-order/delete/{id}', [ProcessPrepressController::class, 'deleteJobOrderPrepress'])->name('prepress.job-order.delete');
        Route::post('prepress/job-order/submit-approval', [ProcessPrepressController::class, 'submitJobOrderPrepressApproval'])->name('prepress.job-order.submit-approval');
        Route::post('prepress/job-order/approve', [ProcessPrepressController::class, 'approveJobOrderPrepress'])->name('prepress.job-order.approve');
        Route::post('prepress/job-order/reject', [ProcessPrepressController::class, 'rejectJobOrderPrepress'])->name('prepress.job-order.reject');

        Route::post('prepress/job-order/reject-job-order', [ProcessPrepressController::class, 'rejectJobOrderPrepress'])->name('prepress.job-order.reject-job-order');
        Route::post('prepress/job-order/reject-job-order-data', [ProcessPrepressController::class, 'rejectJobOrderPrepressData'])->name('prepress.job-order.reject-job-order-data');

        // Timeline routes
        Route::post('prepress/timeline/data', [ProcessPrepressController::class, 'getTimelineData'])->name('prepress.timeline.data');

        // Report routes
        Route::post('prepress/report/data', [ReportController::class, 'getReportData'])->name('prepress.report.data');
        Route::post('prepress/report/export', [ReportController::class, 'exportReport'])->name('prepress.report.export');
        Route::get('report-transportation-cost', [ReportController::class, 'indexReportTransportationCost'])->name('report.transportation-cost.index');
        Route::post('report-transportation-cost/data', [ReportController::class, 'getReportDataTransportationCost'])->name('report.transportation-cost.data');
        Route::post('report-transportation-cost/export', [ReportController::class, 'exportTransportationCost'])->name('report.transportation-cost.export');
        Route::get('test-mysql3-connection', [ReportController::class, 'testMysql3Connection'])->name('test.mysql3.connection');
        Route::get('work-order-percentage', [ReportController::class, 'indexWorkOrderPercentage'])->name('report.work-order-percentage.index');
        Route::post('work-order-percentage/data', [ReportController::class, 'getReportDataWorkOrderPercentage'])->name('report.work-order-percentage.data');
        Route::post('work-order-percentage/export', [ReportController::class, 'exportWorkOrderPercentage'])->name('report.work-order-percentage.export');

        // REPORT SUPPLIER ARRIVAL
        Route::get('report-supplier-arrival', [AdminSupplierTicketController::class, 'supplierArrivalReport'])->name('admin.supplier-tickets.supplier-arrival-report');

        // Monitoring SO
        Route::get('monitoring-so', [MonitoringController::class, 'indexMonitoringSO'])->name('monitoring-so.index');
        Route::post('monitoring-so/data', [MonitoringController::class, 'getMonitoringData'])->name('monitoring-so.data');

        // Notification routes
        Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'getUnreadNotifications'])->name('notifications.unread');
        Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
        Route::patch('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::patch('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/notifications/all', [App\Http\Controllers\NotificationController::class, 'getAllNotifications'])->name('notifications.all');
        Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');


        // Route WOrk Order Good Issue Routes
        Route::get('report-work-order-good-issue', [ReportController::class, 'indexReportWorkOrderGoodIssue'])->name('report.work-order-good-issue.index');
        Route::post('report-work-order-good-issue/data', [ReportController::class, 'getReportDataWorkOrderGoodIssue'])->name('report.work-order-good-issue.data');


        // Master Email Notification Settings routes
        Route::resource('email-notification-settings', App\Http\Controllers\EmailNotificationSettingController::class);
        Route::patch('email-notification-settings/{id}/toggle-status', [App\Http\Controllers\EmailNotificationSettingController::class, 'toggleStatus'])->name('email-notification-settings.toggle-status');


        Route::prefix('development')->group(function () {
            Route::get('job-development', [DevelopmentController::class, 'indexJobOrderDevelopment'])->name('development.job-development.index');
            Route::get('job-development/data', [DevelopmentController::class, 'jobOrderDevelopmentData'])->name('development.job-development.data');

            // Marketing Input Routes
            Route::get('development-input', [DevelopmentController::class, 'marketingInputForm'])->name('development.marketing-input.form');
            Route::post('development-input', [DevelopmentController::class, 'marketingInputStore'])->name('development.marketing-input.store');

            // Development Input Routes (mirip prepress job order)
            Route::get('development-input-form', [DevelopmentController::class, 'developmentInputForm'])->name('development.development-input.form');
            Route::post('development-input-form', [DevelopmentController::class, 'developmentInputStore'])->name('development.development-input.store');

            // Development Input View & Edit Routes
            Route::get('development-input/{id}/view', [DevelopmentController::class, 'developmentInputView'])->name('development.marketing-input.view');
            Route::get('development-input/{id}/edit', [DevelopmentController::class, 'developmentInputEdit'])->name('development.marketing-input.edit');
            Route::post('development/job-order/get-unit-job', [DevelopmentController::class, 'getUnitJob'])->name('development.job-order.get-unit-job');

            // Email Preview Route
            Route::get('preview-email-template', [DevelopmentController::class, 'previewEmailTemplate'])->name('development.preview-email-template');
            Route::get('preview-prepress-email-template', [DevelopmentController::class, 'previewPrepressEmailTemplate'])->name('development.preview-prepress-email-template');

            // Prepress Dashboard Routes
            Route::get('prepress/dashboard/load-data', [ProcessPrepressController::class, 'getPicLoadData'])->name('prepress.dashboard.load-data');
            Route::get('prepress/dashboard/detail-jobs/{id}', [ProcessPrepressController::class, 'getDetailJobs'])->name('prepress.dashboard.detail-jobs');

            // PIC Prepress Email Preview Route
            Route::get('preview-pic-prepress-email-template', [DevelopmentController::class, 'previewPicPrepressEmailTemplate'])->name('development.preview-pic-prepress-email-template');

            // Finish Prepress Email Preview Route
            Route::get('preview-finish-prepress-email-template', [DevelopmentController::class, 'previewFinishPrepressEmailTemplate'])->name('development.preview-finish-prepress-email-template');
            Route::get('preview-proses-produksi-email-template', [DevelopmentController::class, 'previewProsesProduksiEmailTemplate'])->name('development.preview-proses-produksi-email-template');
            Route::get('preview-job-deadline-fulltime-email-template', [DevelopmentController::class, 'previewJobDeadlineFulltimeEmailTemplate'])->name('development.preview-job-deadline-fulltime-email-template');
            Route::get('preview-progress-job-email-template', [DevelopmentController::class, 'previewProgressJobEmailTemplate'])->name('development.preview-progress-job-email-template');
            Route::get('preview-progress-job-structured-email-template', [DevelopmentController::class, 'previewProgressJobStructuredEmailTemplate'])->name('development.preview-progress-job-structured-email-template');
            Route::get('preview-input-awal-structured-email-template', [DevelopmentController::class, 'previewInputAwalStructuredEmailTemplate'])->name('development.preview-input-awal-structured-email-template');

            // Development CRUD Routes
            Route::get('development/{id}/edit', [DevelopmentController::class, 'edit'])->name('development.edit');
            Route::put('development/{id}', [DevelopmentController::class, 'update'])->name('development.update');
            Route::get('development/{id}', [DevelopmentController::class, 'show'])->name('development.show');
            Route::delete('development/{id}', [DevelopmentController::class, 'destroy'])->name('development.destroy');
            Route::get('development-jobs', [DevelopmentController::class, 'marketingJobList'])->name('development.marketing-jobs.list');
            Route::get('marketing-jobs/data', [DevelopmentController::class, 'marketingJobData'])->name('development.marketing-jobs.data');

            // Modal & CRUD Routes
            Route::get('job-details/{id}', [DevelopmentController::class, 'getJobDetails'])->name('development.job-details');
            Route::put('update-job/{id}', [DevelopmentController::class, 'updateJob'])->name('development.update-job');
            Route::delete('delete-job/{id}', [DevelopmentController::class, 'deleteJob'])->name('development.delete-job');

            // Test Route (optional)
            Route::get('test-job-code', [DevelopmentController::class, 'testJobCodeGeneration'])->name('development.test-job-code');

            // RnD Planning Routes
            Route::get('rnd-planning', [DevelopmentController::class, 'rndPlanningList'])->name('development.rnd-planning.list');
            Route::get('rnd-planning/data', [DevelopmentController::class, 'rndPlanningData'])->name('development.rnd-planning.data');
            Route::get('rnd-planning/{id}/plan', [DevelopmentController::class, 'rndPlanningForm'])->name('development.rnd-planning.form');
            Route::post('rnd-planning/{id}/plan', [DevelopmentController::class, 'rndPlanningStore'])->name('development.rnd-planning.store');

            // RnD Workspace Routes
            Route::get('rnd-workspace', [DevelopmentController::class, 'rndWorkspace'])->name('development.rnd-workspace.index');
            Route::get('rnd-workspace/data', [DevelopmentController::class, 'rndWorkspaceData'])->name('development.rnd-workspace.data');
            Route::post('rnd-workspace/{id}/assign-to-ppic', [DevelopmentController::class, 'assignJobToPPIC'])->name('development.rnd-workspace.assign-to-ppic');
            Route::get('rnd-workspace/{id}/details', [DevelopmentController::class, 'getRndJobDetails'])->name('development.rnd-workspace.details');

            // Master Proses Development routes
            Route::get('master-proses', [DevelopmentController::class, 'masterProses'])->name('development.master-proses');
            Route::get('master-proses/data', [DevelopmentController::class, 'masterProsesData'])->name('development.master-proses.data');
            Route::get('master-proses/{id}/detail', [DevelopmentController::class, 'masterProsesDetail'])->name('development.master-proses.detail');
            Route::post('master-proses/update-status', [DevelopmentController::class, 'updateProsesStatus'])->name('development.master-proses.update-status');
            Route::post('master-proses/add-proses', [DevelopmentController::class, 'addProses'])->name('development.master-proses.add-proses');
            Route::get('master-proses/get-proses/{id}', [DevelopmentController::class, 'getProses'])->name('development.master-proses.get-proses');
            Route::post('master-proses/update-proses', [DevelopmentController::class, 'updateProses'])->name('development.master-proses.update-proses');
            Route::post('master-proses/delete-proses', [DevelopmentController::class, 'deleteProses'])->name('development.master-proses.delete-proses');

            // RnD Send to Prepress Routes
            Route::post('rnd-workspace/{id}/send-to-prepress', [DevelopmentController::class, 'sendJobToPrepress'])->name('development.rnd-workspace.send-to-prepress');
            Route::get('rnd-workspace/{id}/view', [DevelopmentController::class, 'rndWorkspaceView'])->name('development.rnd-workspace.view');
            Route::get('rnd-workspace/{id}/edit', [DevelopmentController::class, 'rndWorkspaceEdit'])->name('development.rnd-workspace.edit');
            Route::delete('rnd-workspace/{id}/delete', [DevelopmentController::class, 'rndWorkspaceDelete'])->name('development.rnd-workspace.delete');
            // RnD Customer Approval Routes
            Route::post('rnd-workspace/{id}/rnd-customer-approval', [DevelopmentController::class, 'rndCustomerApproval'])->name('development.rnd-customer-approval');

            // Meeting OPP Routes
            Route::get('development/{id}/meeting-opp', [DevelopmentController::class, 'inputMeetingOPP'])->name('development.meeting-opp.input');
            Route::post('development/{id}/meeting-opp', [DevelopmentController::class, 'storeMeetingOPP'])->name('development.meeting-opp.store');
            Route::post('development/{id}/meeting-opp/approve', [DevelopmentController::class, 'approveMeetingOPP'])->name('development.meeting-opp.approve');
            Route::post('development/{id}/meeting-opp/rnd-approve', [DevelopmentController::class, 'rndApproveMeetingOPP'])->name('development.meeting-opp.rnd-approve');
            Route::post('development/{id}/meeting-opp/marketing-approve', [DevelopmentController::class, 'marketingApproveMeetingOPP'])->name('development.meeting-opp.marketing-approve');
            Route::post('development/{id}/return-to-prepress', [DevelopmentController::class, 'returnToPrepress'])->name('development.return-to-prepress');

            // Purchasing Info Routes
            Route::post('development/{id}/update-purchasing-info', [DevelopmentController::class, 'updatePurchasingInfo'])->name('development.update-purchasing-info');
            Route::post('development/{id}/update-all-purchasing-info', [DevelopmentController::class, 'updateAllPurchasingInfo'])->name('development.update-all-purchasing-info');

            // Lead Time Configuration Routes
            Route::post('development/{id}/lead-time', [DevelopmentController::class, 'storeLeadTime'])->name('development.lead-time.store');

            // Scheduling Development Routes
            Route::get('development/{id}/scheduling', [DevelopmentController::class, 'schedulingDevelopment'])->name('development.scheduling.input');
            Route::post('development/{id}/scheduling', [DevelopmentController::class, 'storeScheduling'])->name('development.scheduling.store');
            Route::get('available-machines', [DevelopmentController::class, 'getAvailableMachines'])->name('development.available-machines');
            Route::get('get-machines', [DevelopmentController::class, 'getAvailableMachines'])->name('development.get-machines');

            // Map Proof Routes
            Route::get('development/{id}/map-proof', [DevelopmentController::class, 'uploadMapProof'])->name('development.map-proof.input');
            Route::post('development/{id}/map-proof', [DevelopmentController::class, 'storeMapProof'])->name('development.map-proof.store');
            Route::delete('development/{id}/map-proof/delete-file', [DevelopmentController::class, 'deleteProofFile'])->name('development.map-proof.delete-file');
            Route::post('development/{id}/map-proof/send-customer', [DevelopmentController::class, 'sendToCustomer'])->name('development.map-proof.send-customer');

            // Sales Order Routes
            Route::get('development/{id}/sales-order', [DevelopmentController::class, 'createSalesOrder'])->name('development.sales-order.input');
            Route::post('development/{id}/sales-order', [DevelopmentController::class, 'storeSalesOrder'])->name('development.sales-order.store');

            // Close Development Route
            Route::post('development/{id}/close-development', [DevelopmentController::class, 'closeDevelopment'])->name('development.close-development');

            // PPIC Routes
            Route::get('development/{id}/ppic-form', [DevelopmentController::class, 'ppicForm'])->name('development.ppic-form');
            Route::post('development/{id}/process-schedule', [DevelopmentController::class, 'storeProcessSchedule'])->name('development.process-schedule.store');
            Route::post('development/{id}/production-schedule', [DevelopmentController::class, 'storeProductionSchedule'])->name('development.production-schedule.store');
            Route::get('development/{id}/production-schedule/{scheduleId}', [DevelopmentController::class, 'showProductionSchedule'])->name('development.production-schedule.show');
            Route::put('development/{id}/production-schedule/{scheduleId}', [DevelopmentController::class, 'updateProductionSchedule'])->name('development.production-schedule.update');
            Route::put('development/{id}/production-schedule/{scheduleId}/status', [DevelopmentController::class, 'updateProductionScheduleStatus'])->name('development.production-schedule.update-status');

            // Report Development Routes (harus ditempatkan sebelum route dengan parameter {id})
            Route::get('report', [DevelopmentController::class, 'reportDataDevelopment'])->name('report.development.index');
            Route::get('development/report/data', [DevelopmentController::class, 'getReportDataDevelopment'])->name('report.development.data');
            Route::get('development/report/export/excel', [DevelopmentController::class, 'exportReportDevelopmentExcel'])->name('report.development.export.excel');
            Route::get('development/report/export/pdf', [DevelopmentController::class, 'exportReportDevelopmentPdf'])->name('report.development.export.pdf');

            // Timeline & Report Routes
            Route::get('development/{id}/timeline', [DevelopmentController::class, 'getJobTimeline'])->name('development.timeline');
            Route::get('development/report-data', [DevelopmentController::class, 'getReportData'])->name('development.report');

            // Production Report Routes
            Route::get('development/{id}/production-report', [DevelopmentController::class, 'productionReport'])->name('development.production-report');
            Route::post('development/{id}/production-report', [DevelopmentController::class, 'storeProductionReport'])->name('development.production-report.store');
            Route::post('development/{id}/production-report/{scheduleId}/rnd-approve', [DevelopmentController::class, 'rndApproveProductionReport'])->name('development.production-report.rnd-approve');
            Route::post('development/{id}/production-report/{scheduleId}/revise', [DevelopmentController::class, 'reviseProductionReport'])->name('development.production-report.revise');

            // RnD Production Approval Routes
            Route::get('development/{id}/rnd-production-approval', [DevelopmentController::class, 'rndProductionApproval'])->name('development.rnd-production-approval');
            Route::post('development/{id}/rnd-production-approval/{scheduleId}/approve', [DevelopmentController::class, 'rndApproveProductionReportAjax'])->name('development.rnd-production-approval.approve');

            Route::get('rnd-planning/{id}/edit', [DevelopmentController::class, 'rndPlanningEditForm'])->name('development.rnd-planning.edit');
            Route::put('rnd-planning/{id}/update', [DevelopmentController::class, 'rndPlanningUpdate'])->name('development.rnd-planning.update');
            Route::get('rnd-planning/{id}/processes', [DevelopmentController::class, 'getJobProcesses'])->name('development.rnd-planning.processes');

            // In Progress Processes Routes
            Route::get('in-progress-processes', [DevelopmentController::class, 'inProgressProcesses'])->name('development.in-progress-processes');
            Route::get('in-progress-processes/data', [DevelopmentController::class, 'inProgressProcessesData'])->name('development.in-progress-processes.data');



            // User Execution Routes
            Route::prefix('user-execution')->group(function () {
                Route::get('my-processes', [UserExecutionController::class, 'myProcesses'])->name('user-execution.my-processes');
                Route::get('my-processes/data', [UserExecutionController::class, 'myProcessesData'])->name('user-execution.my-processes.data');
                Route::get('process/{id}/execute', [UserExecutionController::class, 'executeProcess'])->name('user-execution.execute-process');
                Route::post('process/{id}/start', [UserExecutionController::class, 'startProcess'])->name('user-execution.start-process');
                Route::post('process/{id}/complete', [UserExecutionController::class, 'completeProcess'])->name('user-execution.complete-process');

                // Trial Khusus Workflow Routes
                Route::get('process/{id}/data', [UserExecutionController::class, 'getProcessData'])->name('user-execution.process-data');
                Route::post('process/{id}/update-tracking', [UserExecutionController::class, 'updatePurchasingTracking'])->name('user-execution.update-tracking');
                Route::post('process/{id}/qc-verification', [UserExecutionController::class, 'submitQcVerification'])->name('user-execution.qc-verification');

                // PPIC Modal Routes
                Route::post('process/{id}/production-schedule', [UserExecutionController::class, 'submitProductionSchedule'])->name('user-execution.production-schedule');
                Route::post('process/{id}/item-request', [UserExecutionController::class, 'submitItemRequest'])->name('user-execution.item-request');

                // Process History Route
                Route::get('process/{id}/history', [UserExecutionController::class, 'getProcessHistory'])->name('user-execution.process-history');

                // Trial Khusus Form Routes
                Route::get('purchasing-tracking', function () {
                    return view('main.process.development.user-execution.purchasing-tracking-form');
                })->name('user-execution.purchasing-tracking');

                Route::get('qc-verification', function () {
                    return view('main.process.development.user-execution.qc-verification-form');
                })->name('user-execution.qc-verification');
            });
        });

        // Development Email Notification Management Routes
        Route::prefix('development-email-notification-settings')->group(function () {
            Route::get('/', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'index'])->name('development-email-notification-settings.index');
            Route::get('/create', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'create'])->name('development-email-notification-settings.create');
            Route::post('/', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'store'])->name('development-email-notification-settings.store');
            Route::get('/{id}/edit', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'edit'])->name('development-email-notification-settings.edit');
            Route::put('/{id}', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'update'])->name('development-email-notification-settings.update');
            Route::delete('/{id}', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'destroy'])->name('development-email-notification-settings.destroy');
            Route::post('/{id}/toggle-active', [App\Http\Controllers\DevelopmentEmailNotificationController::class, 'toggleActive'])->name('development-email-notification-settings.toggle-active');
        });

        // Test notification route
        Route::get('test-notification', function () {
            try {
                $jobOrder = \App\Models\JobPrepress::first();
                if ($jobOrder) {
                    $notificationService = new \App\Services\NotificationService();
                    $notificationService->sendJobOrderPrepressNotification($jobOrder);

                    // Log untuk debugging
                    \Illuminate\Support\Facades\Log::info('Test notification completed', [
                        'job_order_id' => $jobOrder->id,
                        'timestamp' => now()
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Notification sent successfully!',
                        'job_order' => [
                            'id' => $jobOrder->id,
                            'kode_design' => $jobOrder->kode_design,
                            'product' => $jobOrder->product
                        ],
                        'timestamp' => now()
                    ]);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'No job order found!'
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Test notification failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Notification failed: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 500);
            }
        })->name('test-notification');

        // Check email configuration
        Route::get('check-email-config', function () {
            $config = [
                'mail_driver' => config('mail.default'),
                'mail_from_address' => config('mail.from.address'),
                'mail_from_name' => config('mail.from.name'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_password' => config('mail.mailers.smtp.password') ? '***SET***' : 'NOT SET',
                'mail_encryption' => config('mail.mailers.smtp.scheme'),
                'app_url' => config('app.url'),
                'app_name' => config('app.name'),
            ];

            return response()->json($config);
        })->name('check-email-config');

        // Simple email test route
        Route::get('simple-email-test', function () {
            try {
                // Test basic mail configuration
                $config = [
                    'mail_driver' => config('mail.default'),
                    'mail_from_address' => config('mail.from.address'),
                    'mail_from_name' => config('mail.from.name'),
                    'mail_host' => config('mail.mailers.smtp.host'),
                    'mail_port' => config('mail.mailers.smtp.port'),
                    'mail_username' => config('mail.mailers.smtp.username'),
                    'mail_password_set' => config('mail.mailers.smtp.password') ? 'YES' : 'NO',
                ];

                // Test if we can create a mail instance
                $jobOrder = \App\Models\JobPrepress::first();
                if ($jobOrder) {
                    $mail = new \App\Mail\JobOrderPrepressNotification($jobOrder, \App\Models\User::first());

                    return response()->json([
                        'success' => true,
                        'message' => 'Email configuration looks good!',
                        'config' => $config,
                        'job_order' => [
                            'id' => $jobOrder->id,
                            'kode_design' => $jobOrder->kode_design,
                            'product' => $jobOrder->product
                        ],
                        'mail_class_created' => true
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'No job order found for testing',
                    'config' => $config
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'config' => $config ?? [],
                    'error_details' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]
                ], 500);
            }
        })->name('simple-email-test');

        // Check users with email
        Route::get('check-users-email', function () {
            $usersWithEmail = \App\Models\User::whereNotNull('email')->get(['id', 'name', 'email', 'divisi', 'jabatan']);
            $usersWithoutEmail = \App\Models\User::whereNull('email')->get(['id', 'name', 'divisi', 'jabatan']);

            return response()->json([
                'users_with_email' => $usersWithEmail,
                'users_without_email' => $usersWithoutEmail,
                'total_users' => \App\Models\User::count(),
                'users_with_email_count' => $usersWithEmail->count(),
                'users_without_email_count' => $usersWithoutEmail->count()
            ]);
        })->name('check-users-email');

        // Fix user email route
        Route::get('fix-user-email', function () {
            try {
                // Fix Ike Herlina email
                $ike = \App\Models\User::find(16);
                if ($ike) {
                    $ike->email = 'jalu.bagaskara@krisanthium.com';
                    $ike->save();
                }

                // Add email for some users if needed
                $usersToUpdate = [
                    ['id' => 4, 'email' => 'jalu.bagaskara@krisanthium.com'], // Jalu Dwi Bagaskara
                    ['id' => 7, 'email' => 'fandi.zakaria@krisanthium.com'], // Administrator
                ];

                foreach ($usersToUpdate as $userData) {
                    $user = \App\Models\User::find($userData['id']);
                    if ($user && !$user->email) {
                        $user->email = $userData['email'];
                        $user->save();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'User emails updated successfully!',
                    'updated_users' => $usersToUpdate
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ], 500);
            }
        })->name('fix-user-email');

        // Direct email test route
        Route::get('test-direct-email', function () {
            try {
                // Test email langsung ke Fandi
                $testEmail = 'jalu.bagaskara@krisanthium.com';

                // Buat job order dummy untuk testing
                $jobOrder = \App\Models\JobPrepress::first();
                if (!$jobOrder) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No job order found for testing'
                    ]);
                }

                // Kirim email langsung
                $mail = new \App\Mail\JobOrderPrepressNotification($jobOrder, \App\Models\User::find(3)); // Fandi
                \Illuminate\Support\Facades\Mail::to($testEmail)->send($mail);

                return response()->json([
                    'success' => true,
                    'message' => 'Test email sent successfully to ' . $testEmail,
                    'job_order' => [
                        'id' => $jobOrder->id,
                        'kode_design' => $jobOrder->kode_design,
                        'product' => $jobOrder->product
                    ],
                    'recipient' => $testEmail,
                    'timestamp' => now()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test email: ' . $e->getMessage(),
                    'error_details' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString()
                    ]
                ], 500);
            }
        })->name('test-direct-email');

        // Preview email templates for employee request approval
        Route::get('preview-email/{type}/{id?}', function ($type, $id = null) {
            try {
                // Ambil request yang ada atau buat dummy
                $employeeRequest = \App\Models\EmployeeRequest::with('employee')->first();

                if (!$employeeRequest) {
                    return 'Tidak ada EmployeeRequest untuk preview. Buat minimal 1 request dulu.';
                }

                // Ambil user untuk approver
                $approver = \App\Models\User::where('email', '!=', null)->first();
                if (!$approver) {
                    return 'Tidak ada user untuk preview.';
                }

                // Get approval chain
                $approvalService = new \App\Services\ApprovalService();
                $chain = $approvalService->getApprovalChain($employeeRequest);

                // Render email berdasarkan tipe
                switch ($type) {
                    case 'pending':
                        $mail = new \App\Mail\EmployeeRequestPendingMail($employeeRequest, $approver, $chain);
                        break;
                    case 'approved':
                        $mail = new \App\Mail\EmployeeRequestApprovedMail($employeeRequest, $approver, 'head_division', false);
                        break;
                    case 'approved-final':
                        $mail = new \App\Mail\EmployeeRequestApprovedMail($employeeRequest, $approver, 'hr', true);
                        break;
                    case 'rejected':
                        $mail = new \App\Mail\EmployeeRequestRejectedMail($employeeRequest, $approver, 'head_division', 'Alasan penolakan: Tidak ada pengganti tugas');
                        break;
                    default:
                        return 'Tipe tidak valid. Pilih: pending, approved, approved-final, atau rejected';
                }

                // Render dan tampilkan di browser
                return $mail->render();

            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
            }
        })->name('preview-email');

        // Check notification configuration status
        Route::get('check-notification-config', function () {
            try {
                $result = [
                    'email_settings' => [],
                    'users_with_email' => [],
                    'notification_service_status' => 'OK',
                    'recommendations' => []
                ];

                // Check email notification settings
                $emailSettings = \App\Models\EmailNotificationSetting::all();
                foreach ($emailSettings as $setting) {
                    $users = $setting->getActiveUsers();
                    $result['email_settings'][] = [
                        'id' => $setting->id,
                        'name' => $setting->notification_name,
                        'type' => $setting->notification_type,
                        'is_active' => $setting->is_active,
                        'users_count' => $users->count(),
                        'users' => $users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'divisi' => $user->divisi,
                                'jabatan' => $user->jabatan
                            ];
                        })
                    ];
                }

                // Check users with email
                $usersWithEmail = \App\Models\User::whereNotNull('email')->get();
                $result['users_with_email'] = $usersWithEmail->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'divisi' => $user->divisi,
                        'jabatan' => $user->jabatan
                    ];
                });

                // Recommendations
                if (empty($result['email_settings'])) {
                    $result['recommendations'][] = 'No email notification settings found. Run seeder or create manually.';
                }

                if ($result['users_with_email']->count() == 0) {
                    $result['recommendations'][] = 'No users with email addresses found.';
                }

                foreach ($result['email_settings'] as $setting) {
                    if ($setting['users_count'] == 0) {
                        $result['recommendations'][] = "Setting '{$setting['name']}' has no users assigned.";
                    }
                }

                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        })->name('check-notification-config');

        // Run email notification seeder
        Route::get('run-email-seeder', function () {
            try {
                // Check if settings already exist
                $existingSettings = \App\Models\EmailNotificationSetting::where('notification_type', 'job_order_prepress')->count();

                if ($existingSettings > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email notification settings already exist. No need to run seeder.',
                        'existing_count' => $existingSettings
                    ]);
                }

                // Run the seeder
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'EmailNotificationSettingSeeder']);

                return response()->json([
                    'success' => true,
                    'message' => 'Email notification seeder run successfully',
                    'output' => \Illuminate\Support\Facades\Artisan::output()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to run seeder: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        })->name('run-email-seeder');

        // Check recent logs
        Route::get('check-recent-logs', function () {
            try {
                $logFile = storage_path('logs/laravel.log');

                if (!file_exists($logFile)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Log file not found'
                    ]);
                }

                // Get last 50 lines of log
                $lines = file($logFile);
                $recentLines = array_slice($lines, -50);

                $notificationLogs = [];
                foreach ($recentLines as $line) {
                    if (
                        strpos($line, 'notification') !== false ||
                        strpos($line, 'email') !== false ||
                        strpos($line, 'mail') !== false
                    ) {
                        $notificationLogs[] = trim($line);
                    }
                }

                return response()->json([
                    'success' => true,
                    'total_lines' => count($lines),
                    'recent_lines' => count($recentLines),
                    'notification_logs' => $notificationLogs,
                    'last_10_lines' => array_slice($lines, -10)
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read logs: ' . $e->getMessage()
                ], 500);
            }
        })->name('check-recent-logs');




        // TRIAL SAMPLE ROUTES
        Route::prefix('trial')->name('trial.')->group(function () {
            // Trial Samples
            Route::get('samples', [TrialSampleController::class, 'index'])->name('samples.index');
            Route::get('samples/create', [TrialSampleController::class, 'create'])->name('samples.create');
            Route::post('samples', [TrialSampleController::class, 'store'])->name('samples.store');
            Route::get('samples/{trialSample}', [TrialSampleController::class, 'show'])->name('samples.show');

            // Workflow actions
            Route::post('samples/{trialSample}/submit-purchasing', [TrialSampleController::class, 'submitToPurchasing'])->name('samples.submit-purchasing');
            Route::post('samples/{trialSample}/purchasing-approve', [TrialSampleController::class, 'purchasingApprove'])->name('samples.purchasing-approve');
            Route::post('samples/{trialSample}/purchasing-reject', [TrialSampleController::class, 'purchasingReject'])->name('samples.purchasing-reject');
            Route::post('samples/{trialSample}/qa-start-processing', [TrialSampleController::class, 'qaStartProcessing'])->name('samples.qa-start-processing');
            Route::post('samples/{trialSample}/close', [TrialSampleController::class, 'close'])->name('samples.close');

            // Process Steps
            Route::post('samples/{trialSample}/process-steps', [App\Http\Controllers\TrialProcessStepController::class, 'store'])->name('process-steps.store');
            Route::post('process-steps/{processStep}/assign-user', [App\Http\Controllers\TrialProcessStepController::class, 'assignUser'])->name('process-steps.assign-user');
            Route::post('process-steps/{processStep}/start', [App\Http\Controllers\TrialProcessStepController::class, 'startProcess'])->name('process-steps.start');
            Route::post('process-steps/{processStep}/complete', [App\Http\Controllers\TrialProcessStepController::class, 'completeProcess'])->name('process-steps.complete');
            Route::post('process-steps/{processStep}/verify', [App\Http\Controllers\TrialProcessStepController::class, 'verifyProcess'])->name('process-steps.verify');

            // Form Submissions
            Route::get('process-steps/{processStep}/form', [App\Http\Controllers\TrialFormSubmissionController::class, 'showForm'])->name('form.show');
            Route::post('process-steps/{processStep}/form', [App\Http\Controllers\TrialFormSubmissionController::class, 'store'])->name('form.store');
            Route::post('process-steps/{processStep}/form-submit', [App\Http\Controllers\TrialFormSubmissionController::class, 'submit'])->name('form.submit');
            Route::post('form-submissions/{formSubmission}/verify', [App\Http\Controllers\TrialFormSubmissionController::class, 'verify'])->name('form.verify');
            Route::get('form-submissions/{formSubmission}', [App\Http\Controllers\TrialFormSubmissionController::class, 'show'])->name('form.show-submission');
        });

        // Legacy routes (for backward compatibility)
        Route::get('data-trial-material', [TrialSampleController::class, 'index'])->name('data-trial-material');
        Route::get('input-pengajuan-trial', [TrialSampleController::class, 'create'])->name('input-pengajuan-trial');

        Route::post('/process/save-plan-update', [ProcessController::class, 'savePlanUpdate'])->name('process.save-plan-update');
        Route::post('/process/save-finish-job', [ProcessController::class, 'saveFinishJob'])->name('process.save-finish-job');
        Route::post('/process/send-job-order-to-machine', [ProcessController::class, 'sendJobOrderToMachine'])->name('process.send-job-order-to-machine');
        Route::get('/process/get-open-job-orders', [ProcessController::class, 'getOpenJobOrdersFromRemote'])->name('process.get-open-job-orders');
        Route::post('/process/get-plan-preview', [ProcessController::class, 'getPlanPreview'])->name('process.get-plan-preview');
        Route::post('/process/get-lubrication-maintenance', [ProcessController::class, 'getLubricationMaintenance'])->name('process.get-lubrication-maintenance');
        Route::get('/process/get-lubrication-machines', [ProcessController::class, 'getLubricationMachines'])->name('process.get-lubrication-machines');
        Route::get('/process/get-holidays', [ProcessController::class, 'getHolidays'])->name('process.get-holidays');
        Route::get('/process/holiday-data', [ProcessController::class, 'getHolidayData'])->name('process.holiday-data');

        // Machine Shift Configuration
        Route::get('/process/machine-shift-config', [ProcessController::class, 'getMachineShiftConfig'])->name('process.machine-shift-config');
        Route::post('/process/machine-shift-config', [ProcessController::class, 'saveMachineShiftConfig'])->name('process.save-machine-shift-config');
        Route::post('/get-machine-mapping', [ProcessController::class, 'getMachineMapping'])->name('get-machine-mapping');

        // HR SYSTEM ROUTES
        Route::prefix('hr')->name('hr.')->group(function () {
            // Dashboard
            Route::get('/dashboard', [HRDashboardController::class, 'index'])->name('dashboard');
            Route::get('/approval-stats', [HRDashboardController::class, 'getApprovalStats'])->name('approval-stats');
            Route::get('/request-trends', [HRDashboardController::class, 'getRequestTrends'])->name('request-trends');

            // Notifications
            Route::post('/notifications/{id}/read', [HRDashboardController::class, 'markNotificationAsRead'])->name('notifications.read');
            Route::post('/notifications/read-all', [HRDashboardController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');

            // Employee Requests
            Route::get('/requests', [HRRequestController::class, 'index'])->name('requests.index');
            Route::get('/requests-prd', [HRRequestController::class, 'indexPrd'])->name('requests.index-prd');
            Route::get('/requests/guide', [HRRequestController::class, 'guide'])->name('requests.guide');
            Route::get('/requests/debug/{id}', [App\Http\Controllers\HRApprovalController::class, 'debugRequest'])->name('requests.debug');
            Route::get('/requests/debug-spv/{id}', [App\Http\Controllers\HRRequestController::class, 'debugSpvCounter'])->name('requests.debug-spv');
            Route::get('/requests/create', [HRRequestController::class, 'create'])->name('requests.create');
            Route::get('/requests/report', [HRRequestController::class, 'report'])->name('requests.report');
            Route::get('/requests/calendar-events', [HRRequestController::class, 'calendarEvents'])->name('requests.calendar-events');
            Route::get('/requests/calendar-request-details', [HRRequestController::class, 'getCalendarRequestDetails'])->name('requests.calendar-request-details');
            Route::get('/requests/department-absence-stats', [HRRequestController::class, 'getDepartmentAbsenceStats'])->name('requests.department-absence-stats');
            Route::post('/requests', [HRRequestController::class, 'store'])->name('requests.store');
            Route::post('/requests/confirm', [HRRequestController::class, 'storeWithConfirmation'])->name('requests.store.confirm');
            Route::get('/requests/{id}', [HRRequestController::class, 'show'])->name('requests.show');
            Route::get('/requests/{id}/edit', [HRRequestController::class, 'edit'])->name('requests.edit');
            Route::put('/requests/{id}', [HRRequestController::class, 'update'])->name('requests.update');
            Route::post('/requests/{id}/cancel', [HRRequestController::class, 'cancel'])->name('requests.cancel');
            Route::post('/requests/{id}/approve', [HRRequestController::class, 'approve'])->name('requests.approve');
            Route::post('/requests/{id}/reject', [HRRequestController::class, 'reject'])->name('requests.reject');

            // Approval Settings Routes
            Route::prefix('approval-settings')->name('approval-settings.')->group(function () {
                // Division Approval Settings (MUST be before {id} routes to avoid conflicts)
                Route::prefix('divisions')->name('divisions.')->group(function () {
                    Route::get('/', [App\Http\Controllers\DivisiApprovalSettingController::class, 'index'])->name('index');
                    Route::put('/{divisiId}', [App\Http\Controllers\DivisiApprovalSettingController::class, 'update'])->name('update');
                    Route::get('/users/{divisiId}', [App\Http\Controllers\DivisiApprovalSettingController::class, 'getUsersByDivision'])->name('users-by-division');
                    Route::get('/preview/{divisiId}', [App\Http\Controllers\DivisiApprovalSettingController::class, 'previewChain'])->name('preview-chain');
                });

                Route::get('/flow/{requestType}', [App\Http\Controllers\ApprovalSettingController::class, 'getApprovalFlow'])->name('flow');
                Route::get('/next-approver', [App\Http\Controllers\ApprovalSettingController::class, 'getNextApprover'])->name('next-approver');
                Route::get('/approvers', [App\Http\Controllers\ApprovalSettingController::class, 'approvers'])->name('approvers');

                Route::get('/', [App\Http\Controllers\ApprovalSettingController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\ApprovalSettingController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\ApprovalSettingController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers\ApprovalSettingController::class, 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers\ApprovalSettingController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\ApprovalSettingController::class, 'destroy'])->name('destroy');
            });

            // Absence Settings Routes
            Route::prefix('absence-settings')->name('absence-settings.')->group(function () {
                Route::get('/', [App\Http\Controllers\AbsenceSettingController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\AbsenceSettingController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\AbsenceSettingController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [App\Http\Controllers\AbsenceSettingController::class, 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers\AbsenceSettingController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\AbsenceSettingController::class, 'destroy'])->name('destroy');
            });

            // Approval Routes
            Route::prefix('approval')->name('approval.')->group(function () {
                Route::get('/dashboard', [App\Http\Controllers\HRApprovalDashboardController::class, 'index'])->name('dashboard');
                Route::get('/supervisor-pending', [HRApprovalController::class, 'supervisorPending'])->name('supervisor-pending');
                Route::get('/manager-pending', [HRApprovalController::class, 'managerPending'])->name('manager-pending');
                Route::get('/general-manager-pending', [HRApprovalController::class, 'generalManagerPending'])->name('general-manager-pending');
                Route::get('/hr-pending', [HRApprovalController::class, 'hrPending'])->name('hr-pending');
                Route::get('/head-pending', [HRApprovalController::class, 'headPending'])->name('head-pending');
                // Aliases agar semua halaman approval berada di bawah /approval
                Route::get('/head-pending-overtime', [App\Http\Controllers\OvertimeController::class, 'headPending'])->name('head-pending-overtime');
                Route::get('/spv-pending', [App\Http\Controllers\OvertimeController::class, 'spvPending'])->name('spv-pending-alias');
                Route::get('/hrga-pending', [App\Http\Controllers\VehicleAssetController::class, 'hrgaPending'])->name('hrga-pending');
                Route::get('/{id}/show', [HRApprovalController::class, 'showApprovalForm'])->name('show');
                Route::post('/{id}/process', [HRApprovalController::class, 'processApproval'])->name('process');
                Route::post('/{id}/disapprove', [HRApprovalController::class, 'disapprove'])->name('disapprove');
                Route::post('/{id}/sync-payroll', [HRApprovalController::class, 'syncToPayroll'])->name('sync-payroll');
                Route::get('/{id}/check-sync-status', [HRApprovalController::class, 'checkSyncStatus'])->name('check-sync-status');
                Route::post('/bulk-approve', [HRApprovalController::class, 'bulkApprove'])->name('bulk-approve');
                Route::get('/history', [HRApprovalController::class, 'approvalHistory'])->name('history');
                Route::get('/stats', [HRApprovalController::class, 'getApprovalStats'])->name('stats');
            });

            // SPL (Surat Perintah Lembur) Routes
            Route::prefix('spl')->name('spl.')->group(function () {
                Route::get('/', [App\Http\Controllers\SplController::class, 'index'])->name('index');
                Route::get('/pending', [App\Http\Controllers\SplController::class, 'pending'])->name('pending');
                Route::get('/create', [App\Http\Controllers\SplController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\SplController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\SplController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers\SplController::class, 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers\SplController::class, 'update'])->name('update');
                Route::get('/{id}/print', [App\Http\Controllers\SplController::class, 'print'])->name('print');
                Route::post('/{id}/mark-signed', [App\Http\Controllers\SplController::class, 'markAsSigned'])->name('mark-signed');
                Route::post('/{id}/process-approval', [App\Http\Controllers\SplController::class, 'processApproval'])->name('process-approval');
                Route::post('/{id}/approve', [App\Http\Controllers\SplController::class, 'approve'])->name('approve'); // Legacy
                Route::post('/{id}/reject', [App\Http\Controllers\SplController::class, 'reject'])->name('reject'); // Legacy
                Route::post('/{id}/upload-signed-document', [App\Http\Controllers\SplController::class, 'uploadSignedDocument'])->name('upload-signed-document');
            });

            // Overtime (SPL) Routes
            Route::prefix('overtime')->name('overtime.')->group(function () {
                // Staff routes - redirect root to index
                Route::get('/', [App\Http\Controllers\OvertimeController::class, 'index'])->name('index');
                Route::get('/index', [App\Http\Controllers\OvertimeController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\OvertimeController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\OvertimeController::class, 'store'])->name('store');

                // SPV routes
                Route::get('/spv-pending', [App\Http\Controllers\OvertimeController::class, 'spvPending'])->name('spv-pending');
                Route::post('/{id}/spv-approve', [App\Http\Controllers\OvertimeController::class, 'spvApprove'])->name('spv-approve');
                Route::post('/{id}/spv-reject', [App\Http\Controllers\OvertimeController::class, 'spvReject'])->name('spv-reject');
                Route::post('/spv-bulk-approve', [App\Http\Controllers\OvertimeController::class, 'spvBulkApprove'])->name('spv-bulk-approve');

                // Head routes
                Route::get('/head-pending', [App\Http\Controllers\OvertimeController::class, 'headPending'])->name('head-pending');
                Route::post('/{id}/head-approve', [App\Http\Controllers\OvertimeController::class, 'headApprove'])->name('head-approve');
                Route::post('/head-bulk-approve', [App\Http\Controllers\OvertimeController::class, 'headBulkApprove'])->name('head-bulk-approve');

                // HRGA routes
                Route::get('/hrga-pending', [App\Http\Controllers\OvertimeController::class, 'hrgaPending'])->name('hrga-pending');
                Route::post('/{id}/hrga-approve', [App\Http\Controllers\OvertimeController::class, 'hrgaApprove'])->name('hrga-approve');
                Route::get('/hrga-approved', [App\Http\Controllers\OvertimeController::class, 'hrgaApproved'])->name('hrga-approved');
            });

            // Vehicle Asset routes
            Route::prefix('vehicle-asset')->name('vehicle-asset.')->group(function () {
                Route::get('/', [App\Http\Controllers\VehicleAssetController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\VehicleAssetController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\VehicleAssetController::class, 'store'])->name('store');
                Route::get('/{id}/show', [App\Http\Controllers\VehicleAssetController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers\VehicleAssetController::class, 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers\VehicleAssetController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\VehicleAssetController::class, 'destroy'])->name('destroy');
                Route::get('/manager-pending', [App\Http\Controllers\VehicleAssetController::class, 'managerPending'])->name('manager-pending');

                // Manager routes untuk Vehicle/Asset approval (action routes tetap di sini)
                Route::post('/{id}/manager-approve', [App\Http\Controllers\VehicleAssetController::class, 'managerApprove'])->name('manager-approve');
                Route::post('/{id}/manager-reject', [App\Http\Controllers\VehicleAssetController::class, 'managerReject'])->name('manager-reject');
                Route::post('/manager-bulk-approve', [App\Http\Controllers\VehicleAssetController::class, 'managerBulkApprove'])->name('manager-bulk-approve');

                // HRGA routes
                Route::get('/hrga-pending', [App\Http\Controllers\VehicleAssetController::class, 'hrgaPending'])->name('hrga-pending');
                Route::post('/{id}/hrga-approve', [App\Http\Controllers\VehicleAssetController::class, 'hrgaApprove'])->name('hrga-approve');
                Route::post('/{id}/hrga-reject', [App\Http\Controllers\VehicleAssetController::class, 'hrgaReject'])->name('hrga-reject');
                Route::get('/hrga-approved', [App\Http\Controllers\VehicleAssetController::class, 'hrgaApproved'])->name('hrga-approved');
            });

            // Employee Data Management
            Route::prefix('employee-data')->name('employee-data.')->group(function () {
                Route::get('/', [EmployeeDataController::class, 'index'])->name('index');
                Route::get('/data', [EmployeeDataController::class, 'data'])->name('data');
                Route::get('/detail/{id}', [EmployeeDataController::class, 'getDetail'])->name('detail');
                Route::get('/create', [EmployeeDataController::class, 'create'])->name('create');
                Route::post('/', [EmployeeDataController::class, 'store'])->name('store');
                Route::get('/import', [EmployeeDataController::class, 'showImport'])->name('import');
                Route::post('/import', [EmployeeDataController::class, 'import'])->name('import.store');
                Route::get('/template', [EmployeeDataController::class, 'downloadTemplate'])->name('template');
                Route::get('/{id}/edit', [EmployeeDataController::class, 'edit'])->name('edit');
                Route::put('/{id}', [EmployeeDataController::class, 'update'])->name('update');
                Route::delete('/{id}', [EmployeeDataController::class, 'destroy'])->name('destroy');
            });

            // Training Management
            Route::prefix('training')->name('training.')->group(function () {
                // Training Dashboard
                Route::get('/dashboard', [App\Http\Controllers\TrainingDashboardController::class, 'index'])->name('dashboard');
                Route::get('/api/stats', [App\Http\Controllers\TrainingDashboardController::class, 'getStats'])->name('api.stats');
                Route::get('/api/registration-trends', [App\Http\Controllers\TrainingDashboardController::class, 'getRegistrationTrends'])->name('api.registration-trends');
                Route::get('/api/approval-trends', [App\Http\Controllers\TrainingDashboardController::class, 'getApprovalTrends'])->name('api.approval-trends');
                Route::get('/api/department-participation', [App\Http\Controllers\TrainingDashboardController::class, 'getDepartmentParticipation'])->name('api.department-participation');
                Route::get('/api/completion-rates', [App\Http\Controllers\TrainingDashboardController::class, 'getCompletionRates'])->name('api.completion-rates');
                Route::get('/api/employee-history', [App\Http\Controllers\TrainingDashboardController::class, 'getEmployeeTrainingHistory'])->name('api.employee-history');
                Route::get('/api/test-connection', [App\Http\Controllers\TrainingDashboardController::class, 'testConnection'])->name('api.test-connection');

                // Training Registration (must be before Training Master routes to avoid conflicts)
                Route::prefix('registration')->name('registration.')->group(function () {
                    Route::get('/', [App\Http\Controllers\TrainingRegistrationController::class, 'index'])->name('index');
                    Route::get('/{id}', [App\Http\Controllers\TrainingRegistrationController::class, 'show'])->name('show');
                    Route::post('/{id}/register', [App\Http\Controllers\TrainingRegistrationController::class, 'register'])->name('register');
                    Route::post('/{id}/cancel', [App\Http\Controllers\TrainingRegistrationController::class, 'cancel'])->name('cancel');
                    Route::get('/history', [App\Http\Controllers\TrainingRegistrationController::class, 'history'])->name('history');
                    Route::get('/statistics', [App\Http\Controllers\TrainingRegistrationController::class, 'statistics'])->name('statistics');
                });

                // Training Management (harus sebelum route /{id})
                Route::prefix('management')->name('management.')->group(function () {
                    Route::get('/', [App\Http\Controllers\TrainingManagementController::class, 'index'])->name('index');
                    Route::get('/{id}', [App\Http\Controllers\TrainingManagementController::class, 'show'])->name('show');
                    Route::get('/api/employees', [App\Http\Controllers\TrainingManagementController::class, 'getEmployees'])->name('get-employees');
                    Route::post('/{id}/register', [App\Http\Controllers\TrainingManagementController::class, 'registerEmployees'])->name('register-employees');
                    Route::post('/{id}/remove/{participantId}', [App\Http\Controllers\TrainingManagementController::class, 'removeEmployee'])->name('remove-employee');
                    Route::post('/{id}/bulk-approve', [App\Http\Controllers\TrainingManagementController::class, 'bulkApprove'])->name('bulk-approve');
                });

                // Training Schedule
                Route::prefix('schedule')->name('schedule.')->group(function () {
                    Route::get('/', [App\Http\Controllers\TrainingScheduleController::class, 'index'])->name('index');
                    Route::get('/create', [App\Http\Controllers\TrainingScheduleController::class, 'create'])->name('create');
                    Route::post('/', [App\Http\Controllers\TrainingScheduleController::class, 'store'])->name('store');
                    Route::get('/{id}', [App\Http\Controllers\TrainingScheduleController::class, 'show'])->name('show');
                    Route::get('/{id}/edit', [App\Http\Controllers\TrainingScheduleController::class, 'edit'])->name('edit');
                    Route::put('/{id}', [App\Http\Controllers\TrainingScheduleController::class, 'update'])->name('update');
                    Route::delete('/{id}', [App\Http\Controllers\TrainingScheduleController::class, 'destroy'])->name('destroy');
                    Route::get('/api/upcoming', [App\Http\Controllers\TrainingScheduleController::class, 'getUpcoming'])->name('upcoming');
                });

                // Training Master
                Route::get('/', [App\Http\Controllers\TrainingController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\TrainingController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\TrainingController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\TrainingController::class, 'show'])->name('show');
                Route::get('/{id}/view', [App\Http\Controllers\TrainingController::class, 'view'])->name('view');
                Route::get('/{id}/edit', [App\Http\Controllers\TrainingController::class, 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers\TrainingController::class, 'update'])->name('update');
                Route::delete('/{id}', [App\Http\Controllers\TrainingController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/publish', [App\Http\Controllers\TrainingController::class, 'publish'])->name('publish');
                Route::get('/api/available', [App\Http\Controllers\TrainingController::class, 'getAvailableTrainings'])->name('api.available');
            });


            // Security Master Routes
            Route::prefix('security-master')->name('security-master.')->group(function () {
                Route::get('/', [App\Http\Controllers\HR\SecurityMasterController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\HR\SecurityMasterController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\HR\SecurityMasterController::class, 'store'])->name('store');
                Route::get('/{securityMaster}', [App\Http\Controllers\HR\SecurityMasterController::class, 'show'])->name('show');
                Route::get('/{securityMaster}/edit', [App\Http\Controllers\HR\SecurityMasterController::class, 'edit'])->name('edit');
                Route::put('/{securityMaster}', [App\Http\Controllers\HR\SecurityMasterController::class, 'update'])->name('update');
                Route::delete('/{securityMaster}', [App\Http\Controllers\HR\SecurityMasterController::class, 'destroy'])->name('destroy');
            });

            // Applicant Routes
            Route::prefix('applicants')->name('applicants.')->group(function () {
                Route::get('/', [App\Http\Controllers\HR\ApplicantController::class, 'index'])->name('index');
                //     Route::get('/create', [App\Http\Controllers\HR\ApplicantController::class, 'create'])->name('create');
                //     Route::post('/', [App\Http\Controllers\HR\ApplicantController::class, 'store'])->name('store');
                //     Route::get('/{applicant}', [App\Http\Controllers\HR\ApplicantController::class, 'show'])->name('show');
                //     Route::get('/{applicant}/edit', [App\Http\Controllers\HR\ApplicantController::class, 'edit'])->name('edit');
                //     Route::put('/{applicant}', [App\Http\Controllers\HR\ApplicantController::class, 'update'])->name('update');
                //     Route::delete('/{applicant}', [App\Http\Controllers\HR\ApplicantController::class, 'destroy'])->name('destroy');

                //     // Test routes
                //     Route::get('/{applicant}/tests', [App\Http\Controllers\HR\ApplicantController::class, 'listTests'])->name('tests');
                //     Route::get('/{applicant}/test-results', [App\Http\Controllers\HR\ApplicantController::class, 'testResults'])->name('test-results');
                //     Route::get('/{applicant}/test-results-json', [App\Http\Controllers\HR\ApplicantController::class, 'getTestResultsJson'])->name('test-results-json');
                //     Route::post('/{applicant}/test-result/{testResult}/confirm', [App\Http\Controllers\HR\ApplicantController::class, 'confirmTestResult'])->name('test-result.confirm');
                //     Route::get('/{applicant}/test-report', [App\Http\Controllers\HR\ApplicantController::class, 'generateTestReport'])->name('test-report');
                //     Route::get('/{applicant}/test/{testType}', [App\Http\Controllers\HR\ApplicantController::class, 'startTest'])->name('test');
                //     Route::post('/{applicant}/test/{testType}', [App\Http\Controllers\HR\ApplicantController::class, 'submitTest'])->name('test.submit');

                //     // Debug route
                //     Route::post('/debug', function (Request $request) {
                //         return response()->json([
                //             'success' => true,
                //             'message' => 'Debug route working',
                //             'data' => $request->all()
                //         ]);
                //     })->name('debug');
            });

            // Ishihara Plates Master Routes
            Route::prefix('ishihara-plates')->name('ishihara-plates.')->group(function () {
                Route::get('/', [App\Http\Controllers\HR\IshiharaPlateController::class, 'index'])->name('index');
            });

            // Math Questions Master Routes
            Route::prefix('math-questions')->name('math-questions.')->group(function () {
                Route::get('/', [App\Http\Controllers\HR\MathQuestionController::class, 'index'])->name('index');
            });

            // HR Reports Routes
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [App\Http\Controllers\HR\HRReportController::class, 'index'])->name('index');
                Route::get('/user-report', [App\Http\Controllers\HR\HRReportController::class, 'userReport'])->name('user-report');
                Route::get('/security-user-report', [App\Http\Controllers\HR\HRReportController::class, 'securityUserReport'])->name('security-user-report');
                Route::get('/request-report', [App\Http\Controllers\HR\HRReportController::class, 'requestReport'])->name('request-report');
                Route::get('/training-report', [App\Http\Controllers\HR\HRReportController::class, 'trainingReport'])->name('training-report');
                Route::get('/training-schedule-report', [App\Http\Controllers\HR\HRReportController::class, 'trainingScheduleReport'])->name('training-schedule-report');
                Route::post('/export-user-report', [App\Http\Controllers\HR\HRReportController::class, 'exportUserReport'])->name('export-user-report');
                Route::post('/export-security-user-report', [App\Http\Controllers\HR\HRReportController::class, 'exportSecurityUserReport'])->name('export-security-user-report');

                // Security Reports
                Route::get('/security', [App\Http\Controllers\HR\HRReportController::class, 'securityReports'])->name('security');
                Route::get('/security/vehicle-checklist', [App\Http\Controllers\HR\HRReportController::class, 'securityVehicleChecklistReport'])->name('security.vehicle-checklist');
                Route::get('/security/goods-movement', [App\Http\Controllers\HR\HRReportController::class, 'securityGoodsMovementReport'])->name('security.goods-movement');
                Route::get('/security/daily-activity', [App\Http\Controllers\HR\HRReportController::class, 'securityDailyActivityReport'])->name('security.daily-activity');

                // Security Export
                Route::get('/security/export/vehicle-checklist', [App\Http\Controllers\HR\HRReportController::class, 'exportSecurityVehicleChecklist'])->name('security.export.vehicle-checklist');
                Route::get('/security/export/goods-movement', [App\Http\Controllers\HR\HRReportController::class, 'exportSecurityGoodsMovement'])->name('security.export.goods-movement');
                Route::get('/security/export/daily-activity', [App\Http\Controllers\HR\HRReportController::class, 'exportSecurityDailyActivity'])->name('security.export.daily-activity');
            });

            // Training Validation Routes
            Route::prefix('training-validation')->name('training-validation.')->group(function () {
                Route::get('/', [App\Http\Controllers\HR\TrainingValidationController::class, 'index'])->name('index');
                Route::get('/{scheduleId}', [App\Http\Controllers\HR\TrainingValidationController::class, 'show'])->name('show');
                Route::post('/{scheduleId}/validate', [App\Http\Controllers\HR\TrainingValidationController::class, 'validateAttendance'])->name('validate');
                Route::get('/{scheduleId}/reschedule', [App\Http\Controllers\HR\TrainingValidationController::class, 'rescheduleForm'])->name('reschedule');
                Route::post('/{scheduleId}/reschedule', [App\Http\Controllers\HR\TrainingValidationController::class, 'reschedule'])->name('reschedule.store');
                Route::post('/{scheduleId}/mark-completed', [App\Http\Controllers\HR\TrainingValidationController::class, 'markCompleted'])->name('mark-completed');
                Route::get('/statistics', [App\Http\Controllers\HR\TrainingValidationController::class, 'statistics'])->name('statistics');
            });

            // Portal Training Karyawan Routes
            Route::prefix('portal-training')->name('portal-training.')->group(function () {
                // Portal Dashboard (untuk karyawan)
                Route::get('/', [PortalTrainingController::class, 'index'])->name('index');
                Route::post('/start/{id}', [PortalTrainingController::class, 'start'])->name('start');

                // Materi Routes
                Route::prefix('materials')->name('materials.')->group(function () {
                    Route::get('/', [App\Http\Controllers\PortalTraining\MaterialController::class, 'index'])->name('index');
                    Route::get('/{id}', [App\Http\Controllers\PortalTraining\MaterialController::class, 'show'])->name('show');
                    Route::post('/{id}/watch', [App\Http\Controllers\PortalTraining\MaterialController::class, 'updateProgress'])->name('watch');
                });

                // Ujian Routes
                Route::prefix('exams')->name('exams.')->group(function () {
                    Route::get('/{materialId}', [App\Http\Controllers\PortalTraining\ExamController::class, 'show'])->name('show');
                    Route::post('/{materialId}/start', [App\Http\Controllers\PortalTraining\ExamController::class, 'start'])->name('start');
                    Route::post('/{examId}/answer', [App\Http\Controllers\PortalTraining\ExamController::class, 'submitAnswer'])->name('answer');
                    Route::post('/{examId}/finish', [App\Http\Controllers\PortalTraining\ExamController::class, 'finish'])->name('finish');
                    Route::get('/{examId}/result', [App\Http\Controllers\PortalTraining\ExamController::class, 'result'])->name('result');
                });

                // Sessions Routes (untuk karyawan menjalankan training sessions)
                Route::prefix('sessions')->name('sessions.')->group(function () {
                    Route::get('/{assignmentId}/{sessionId}', [PortalTrainingController::class, 'showSession'])->name('show');
                    Route::post('/{assignmentId}/{sessionId}/start', [PortalTrainingController::class, 'startSession'])->name('start');
                    Route::post('/{assignmentId}/{sessionId}/submit', [PortalTrainingController::class, 'submitSession'])->name('submit');
                    Route::post('/{assignmentId}/{sessionId}/retry', [PortalTrainingController::class, 'retrySession'])->name('retry');
                });

                // Video Streaming Route (untuk Google Drive video dengan kontrol penuh)
                Route::get('/video/stream/{fileId}', [PortalTrainingController::class, 'streamVideo'])->name('video.stream');

                // Scores/Results Routes
                Route::prefix('scores')->name('scores.')->group(function () {
                    Route::get('/', [PortalTrainingController::class, 'scoresIndex'])->name('index');
                    Route::get('/{assignmentId}', [PortalTrainingController::class, 'viewScores'])->name('show');
                });
                Route::get('/history', [PortalTrainingController::class, 'history'])->name('history');

                // Master Data Routes (untuk trainer/admin)
                Route::prefix('master')->name('master.')->group(function () {
                    // Dashboard
                    Route::get('/dashboard', [PortalTrainingController::class, 'masterDashboard'])->name('dashboard.index');

                    // Test Google Drive API
                    Route::get('/test-google-drive', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'testGoogleDriveApi'])->name('test-google-drive');

                    // Material Categories
                    Route::prefix('categories')->name('categories.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\Master\MaterialCategoryController::class, 'index'])->name('index');
                        Route::get('/data', [App\Http\Controllers\PortalTraining\Master\MaterialCategoryController::class, 'getData'])->name('getData');
                        Route::get('/{id}', [App\Http\Controllers\PortalTraining\Master\MaterialCategoryController::class, 'show'])->name('show');
                        Route::post('/', [App\Http\Controllers\PortalTraining\Master\MaterialCategoryController::class, 'store'])->name('store');
                        Route::put('/{id}', [App\Http\Controllers\PortalTraining\Master\MaterialCategoryController::class, 'update'])->name('update');
                        Route::delete('/{id}', [App\Http\Controllers\PortalTraining\Master\MaterialCategoryController::class, 'destroy'])->name('destroy');
                    });

                    // Difficulty Levels
                    Route::prefix('difficulty-levels')->name('difficulty-levels.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\Master\DifficultyLevelController::class, 'index'])->name('index');
                        Route::get('/data', [App\Http\Controllers\PortalTraining\Master\DifficultyLevelController::class, 'getData'])->name('getData');
                        Route::get('/{id}', [App\Http\Controllers\PortalTraining\Master\DifficultyLevelController::class, 'show'])->name('show');
                        Route::post('/', [App\Http\Controllers\PortalTraining\Master\DifficultyLevelController::class, 'store'])->name('store');
                        Route::put('/{id}', [App\Http\Controllers\PortalTraining\Master\DifficultyLevelController::class, 'update'])->name('update');
                        Route::delete('/{id}', [App\Http\Controllers\PortalTraining\Master\DifficultyLevelController::class, 'destroy'])->name('destroy');
                    });

                    // Materials
                    Route::prefix('materials')->name('materials.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'index'])->name('index');
                        Route::get('/data', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'getData'])->name('getData');
                        Route::get('/create', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'create'])->name('create');
                        Route::post('/', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'store'])->name('store');
                        Route::get('/{id}', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'show'])->name('show');
                        Route::get('/{id}/edit', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'edit'])->name('edit');
                        Route::put('/{id}', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'update'])->name('update');
                        Route::delete('/{id}', [App\Http\Controllers\PortalTraining\Master\MaterialController::class, 'destroy'])->name('destroy');
                    });

                    // Question Banks
                    Route::prefix('question-banks')->name('question-banks.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'index'])->name('index');
                        Route::get('/data', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'getData'])->name('getData');
                        Route::get('/create', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'create'])->name('create');
                        Route::post('/', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'store'])->name('store');
                        Route::post('/import', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'import'])->name('import');
                        Route::get('/download-template', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'downloadTemplate'])->name('download-template');
                        Route::get('/{id}', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'show'])->name('show');
                        Route::get('/{id}/edit', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'edit'])->name('edit');
                        Route::put('/{id}', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'update'])->name('update');
                        Route::delete('/{id}', [App\Http\Controllers\PortalTraining\Master\QuestionBankController::class, 'destroy'])->name('destroy');
                    });

                    // Training Masters
                    Route::prefix('training-masters')->name('training-masters.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'index'])->name('index');
                        Route::get('/data', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'getData'])->name('getData');
                        Route::get('/create', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'create'])->name('create');
                        Route::post('/', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'store'])->name('store');
                        Route::get('/{id}', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'show'])->name('show');
                        Route::get('/{id}/edit', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'edit'])->name('edit');
                        Route::put('/{id}', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'update'])->name('update');
                        Route::delete('/{id}', [App\Http\Controllers\PortalTraining\Master\TrainingMasterController::class, 'destroy'])->name('destroy');
                    });

                    // Assignments
                    Route::prefix('assignments')->name('assignments.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'index'])->name('index');
                        Route::get('/data', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'getData'])->name('getData');
                        Route::get('/create', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'create'])->name('create');
                        Route::post('/', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'store'])->name('store');
                        Route::post('/bulk-assign', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'bulkAssign'])->name('bulkAssign');
                        Route::get('/session/{code}', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'viewSession'])->name('viewSession');
                        Route::post('/session/{code}/start', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'startSession'])->name('startSession');
                        Route::delete('/session/{code}', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'destroySession'])->name('destroySession');
                        Route::post('/{id}/start', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'start'])->name('start');
                        Route::get('/{id}', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'show'])->name('show');
                        Route::get('/{id}/edit', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'edit'])->name('edit');
                        Route::put('/{id}', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'update'])->name('update');
                        Route::delete('/{id}', [App\Http\Controllers\PortalTraining\Master\AssignmentController::class, 'destroy'])->name('destroy');
                    });

                    // Reports
                    Route::prefix('reports')->name('reports.')->group(function () {
                        Route::get('/', [App\Http\Controllers\PortalTraining\PortalTrainingReportController::class, 'index'])->name('index');
                        Route::get('/export', [App\Http\Controllers\PortalTraining\PortalTrainingReportController::class, 'exportExcel'])->name('export');
                    });
                });
            });
        });
    });

    // System Logs Routes (Admin Only)
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/system-logs', [App\Http\Controllers\SystemLogController::class, 'index'])->name('admin.system-logs.index');
        Route::get('/system-logs/{id}', [App\Http\Controllers\SystemLogController::class, 'show'])->name('admin.system-logs.show');
        Route::get('/system-logs/export/csv', [App\Http\Controllers\SystemLogController::class, 'export'])->name('admin.system-logs.export');
        Route::get('/system-logs/statistics', [App\Http\Controllers\SystemLogController::class, 'statistics'])->name('admin.system-logs.statistics');
        Route::get('/system-logs/recent', [App\Http\Controllers\SystemLogController::class, 'recent'])->name('admin.system-logs.recent');
        Route::get('/system-logs/record/{identifier}', [App\Http\Controllers\SystemLogController::class, 'byRecord'])->name('admin.system-logs.by-record');
        Route::get('/system-logs/user/{userId}', [App\Http\Controllers\SystemLogController::class, 'byUser'])->name('admin.system-logs.by-user');
        Route::delete('/system-logs/clean', [App\Http\Controllers\SystemLogController::class, 'clean'])->name('admin.system-logs.clean');
        Route::get('/system-logs/api/log-types', [App\Http\Controllers\SystemLogController::class, 'getLogTypes'])->name('admin.system-logs.api.log-types');
        Route::get('/system-logs/api/action-types', [App\Http\Controllers\SystemLogController::class, 'getActionTypes'])->name('admin.system-logs.api.action-types');
        Route::get('/system-logs/api/table-names', [App\Http\Controllers\SystemLogController::class, 'getTableNames'])->name('admin.system-logs.api.table-names');
    });

    // Supplier Ticketing Routes (Legacy - using 'supplier' prefix)
    Route::prefix('supplier')->middleware(['auth'])->group(function () {
        Route::get('/', [SupplierTicketController::class, 'index'])->name('supplier-tickets.index');
        Route::get('/create', [SupplierTicketController::class, 'create'])->name('supplier-tickets.create');
        Route::post('/', [SupplierTicketController::class, 'store'])->name('supplier-tickets.store');
        Route::get('/{supplierTicket}', [SupplierTicketController::class, 'show'])->name('supplier-tickets.show');
        Route::get('/{supplierTicket}/edit', [SupplierTicketController::class, 'edit'])->name('supplier-tickets.edit');
        Route::put('/{supplierTicket}', [SupplierTicketController::class, 'update'])->name('supplier-tickets.update');
        Route::delete('/{supplierTicket}', [SupplierTicketController::class, 'destroy'])->name('supplier-tickets.destroy');

        // Action routes
        Route::post('/{supplierTicket}/approve', [SupplierTicketController::class, 'approve'])->name('supplier-tickets.approve');
        Route::post('/{supplierTicket}/reject', [SupplierTicketController::class, 'reject'])->name('supplier-tickets.reject');
        Route::post('/{supplierTicket}/process', [SupplierTicketController::class, 'process'])->name('supplier-tickets.process');
        Route::post('/{supplierTicket}/complete', [SupplierTicketController::class, 'complete'])->name('supplier-tickets.complete');

        // Statistics
        Route::get('/api/statistics', [SupplierTicketController::class, 'statistics'])->name('supplier-tickets.statistics');

        // API routes
        Route::get('/api/search-po', [SupplierTicketController::class, 'searchPO'])->name('supplier-tickets.search-po');
        Route::get('/api/get-po-details', [SupplierTicketController::class, 'getPODetails'])->name('supplier-tickets.get-po-details');
        Route::get('/api/get-supplier-info', [SupplierTicketController::class, 'getSupplierInfo'])->name('supplier-tickets.get-supplier-info');
        Route::get('/api/my-pos', [SupplierTicketController::class, 'listMyPOs'])->name('supplier-tickets.my-pos');

        // Dashboard data routes
        Route::get('/api/dashboard-data', [SupplierTicketController::class, 'dashboardData'])->name('supplier-tickets.dashboard-data');
        Route::get('/api/dashboard-tickets', [SupplierTicketController::class, 'dashboardTickets'])->name('supplier-tickets.dashboard-tickets');
    });

    // Supplier Ticketing Routes (New - using 'supplier-tickets' prefix for consistency)
    Route::prefix('supplier-tickets')->middleware(['auth'])->group(function () {
        Route::get('/', [SupplierTicketController::class, 'index'])->name('supplier-tickets.index');
        Route::get('/create', [SupplierTicketController::class, 'create'])->name('supplier-tickets.create');
        Route::post('/', [SupplierTicketController::class, 'store'])->name('supplier-tickets.store');
        Route::get('/{supplierTicket}', [SupplierTicketController::class, 'show'])->name('supplier-tickets.show');
        Route::get('/{supplierTicket}/edit', [SupplierTicketController::class, 'edit'])->name('supplier-tickets.edit');
        Route::put('/{supplierTicket}', [SupplierTicketController::class, 'update'])->name('supplier-tickets.update');
        Route::delete('/{supplierTicket}', [SupplierTicketController::class, 'destroy'])->name('supplier-tickets.destroy');

        // Action routes
        Route::post('/{supplierTicket}/approve', [SupplierTicketController::class, 'approve'])->name('supplier-tickets.approve');
        Route::post('/{supplierTicket}/reject', [SupplierTicketController::class, 'reject'])->name('supplier-tickets.reject');
        Route::post('/{supplierTicket}/process', [SupplierTicketController::class, 'process'])->name('supplier-tickets.process');
        Route::post('/{supplierTicket}/complete', [SupplierTicketController::class, 'complete'])->name('supplier-tickets.complete');

        // Statistics
        Route::get('/api/statistics', [SupplierTicketController::class, 'statistics'])->name('supplier-tickets.statistics');

        // API routes
        Route::get('/api/search-po', [SupplierTicketController::class, 'searchPO'])->name('supplier-tickets.search-po');
        Route::get('/api/get-po-details', [SupplierTicketController::class, 'getPODetails'])->name('supplier-tickets.get-po-details');
        Route::get('/api/get-supplier-info', [SupplierTicketController::class, 'getSupplierInfo'])->name('supplier-tickets.get-supplier-info');
        Route::get('/api/my-pos', [SupplierTicketController::class, 'listMyPOs'])->name('supplier-tickets.my-pos');

        // Dashboard data routes
        Route::get('/api/dashboard-data', [SupplierTicketController::class, 'dashboardData'])->name('supplier-tickets.dashboard-data');
        Route::get('/api/dashboard-tickets', [SupplierTicketController::class, 'dashboardTickets'])->name('supplier-tickets.dashboard-tickets');
    });

    // Admin Supplier Ticketing Routes
    Route::prefix('admin/supplier-tickets')->middleware(['auth'])->group(function () {
        Route::get('/', [AdminSupplierTicketController::class, 'index'])->name('admin.supplier-tickets.index');
        Route::get('/{supplierTicket}', [AdminSupplierTicketController::class, 'show'])->name('admin.supplier-tickets.show');
        Route::post('/{supplierTicket}/reject', [AdminSupplierTicketController::class, 'reject'])->name('admin.supplier-tickets.reject');
        Route::get('/{supplierTicket}/reject-form', [AdminSupplierTicketController::class, 'showRejectForm'])->name('admin.supplier-tickets.reject-form');
        Route::post('/{supplierTicket}/process-reject', [AdminSupplierTicketController::class, 'processReject'])->name('admin.supplier-tickets.process-reject');
        Route::post('/{supplierTicket}/reset-to-processed', [AdminSupplierTicketController::class, 'resetToProcessed'])->name('admin.supplier-tickets.reset-to-processed');
        Route::get('/{supplierTicket}/create-grd-form', [AdminSupplierTicketController::class, 'showCreateGRDForm'])->name('admin.supplier-tickets.create-grd-form');
        Route::get('/{supplierTicket}/create-pqc-form', [AdminSupplierTicketController::class, 'showCreatePQCForm'])->name('admin.supplier-tickets.create-pqc-form');
        Route::get('/{supplierTicket}/combined-form', [AdminSupplierTicketController::class, 'showCombinedForm'])->name('admin.supplier-tickets.combined-form');
        Route::get('/{supplierTicket}/create-document', [AdminSupplierTicketController::class, 'showCreateDocument'])->name('admin.supplier-tickets.create-document');
        Route::post('/{supplierTicket}/create-grd', [AdminSupplierTicketController::class, 'createGRD'])->name('admin.supplier-tickets.create-grd');
        Route::post('/{supplierTicket}/create-pqc', [AdminSupplierTicketController::class, 'createPQC'])->name('admin.supplier-tickets.create-pqc');
        Route::post('/{supplierTicket}/process-combined', [AdminSupplierTicketController::class, 'processCombined'])->name('admin.supplier-tickets.process-combined');
        Route::get('/{supplierTicket}/rejection-letter', [AdminSupplierTicketController::class, 'generateRejectionLetter'])->name('admin.supplier-tickets.rejection-letter');
        Route::post('/{supplierTicket}/send-rejection-email', [AdminSupplierTicketController::class, 'sendRejectionEmail'])->name('admin.supplier-tickets.send-rejection-email');
        Route::post('/generate-tagno', [AdminSupplierTicketController::class, 'generateTagNo'])->name('admin.supplier-tickets.generate-tagno');
        Route::post('/generate-tagno-pqc', [AdminSupplierTicketController::class, 'generateTagNoPQC'])->name('admin.supplier-tickets.generate-tagno-pqc');
        Route::post('/generate-doc-number', [AdminSupplierTicketController::class, 'generateDocNumber'])->name('admin.supplier-tickets.generate-doc-number');
        Route::post('/{supplierTicket}/process-grd-reject', [AdminSupplierTicketController::class, 'processGRDReject'])->name('admin.supplier-tickets.process-grd-reject');
        Route::get('/statistics/overview', [AdminSupplierTicketController::class, 'statistics'])->name('admin.supplier-tickets.statistics');
        Route::post('/supplier-arrival-data', [AdminSupplierTicketController::class, 'getSupplierArrivalData'])->name('admin.supplier-tickets.supplier-arrival-data');
        Route::post('/export-supplier-arrival', [AdminSupplierTicketController::class, 'exportSupplierArrivalData'])->name('admin.supplier-tickets.export-supplier-arrival');
    });


    // GRD Routes
    Route::prefix('grd')->middleware(['auth'])->group(function () {
        Route::get('/', [GRDController::class, 'index'])->name('grd.index');
        Route::get('/create', [GRDController::class, 'create'])->name('grd.create');
        Route::post('/', [GRDController::class, 'store'])->name('grd.store');
        Route::get('/{supplierTicket}', [GRDController::class, 'show'])->name('grd.show');

        // API routes
        Route::get('/api/search-po', [GRDController::class, 'searchPO'])->name('grd.search-po');
        Route::get('/api/get-po-details', [GRDController::class, 'getPODetails'])->name('grd.get-po-details');
    });

    // API for Supplier Timeline
    Route::get('/api/supplier-timeline', function () {
        $completedTickets = \App\Models\SupplierTicket::where('status', 'completed')
            ->whereNotNull('delivery_date')
            ->orderBy('delivery_date', 'desc')
            ->limit(20)
            ->get();

        $stats = [
            'total_completed' => \App\Models\SupplierTicket::where('status', 'completed')->count(),
            'today_deliveries' => \App\Models\SupplierTicket::where('status', 'completed')
                ->whereDate('delivery_date', today())->count(),
            'this_week_deliveries' => \App\Models\SupplierTicket::where('status', 'completed')
                ->whereBetween('delivery_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'unique_suppliers' => \App\Models\SupplierTicket::where('status', 'completed')
                ->distinct('supplier_name')->count('supplier_name')
        ];

        return response()->json([
            'success' => true,
            'data' => $completedTickets,
            'stats' => $stats
        ]);
    });


    // ===== SECURITY REPORTS ROUTES =====
    Route::prefix('security')->name('security.')->group(function () {

        // Security Dashboard Data Routes
        Route::get('/dashboard-data', [SecurityReportController::class, 'dashboardData'])->name('dashboard-data');

        // Vehicle Checklist Routes
        Route::prefix('vehicle-checklist')->name('vehicle-checklist.')->group(function () {
            Route::get('/', [SecurityReportController::class, 'vehicleChecklistIndex'])->name('index');
            Route::get('/create', [SecurityReportController::class, 'vehicleChecklistCreate'])->name('create');
            Route::post('/', [SecurityReportController::class, 'vehicleChecklistStore'])->name('store');
            Route::get('/{id}', [SecurityReportController::class, 'vehicleChecklistShow'])->name('show');
            Route::get('/{id}/edit', [SecurityReportController::class, 'vehicleChecklistEdit'])->name('edit');
            Route::put('/{id}', [SecurityReportController::class, 'vehicleChecklistUpdate'])->name('update');
            Route::put('/{id}/return', [SecurityReportController::class, 'vehicleChecklistReturn'])->name('return');
            Route::delete('/{id}', [SecurityReportController::class, 'vehicleChecklistDestroy'])->name('destroy');
            Route::get('/export/data', [SecurityReportController::class, 'vehicleChecklistExport'])->name('export');
            Route::get('/dashboard/summary', [SecurityReportController::class, 'vehicleChecklistDashboard'])->name('dashboard');
            Route::get('/dashboard-data', [SecurityReportController::class, 'vehicleChecklistDashboardData'])->name('dashboard-data');
        });

        // Goods Movement Routes
        Route::prefix('goods-movement')->name('goods-movement.')->group(function () {
            Route::get('/', [SecurityReportController::class, 'goodsMovementIndex'])->name('index');
            Route::get('/create', [SecurityReportController::class, 'goodsMovementCreate'])->name('create');
            Route::post('/', [SecurityReportController::class, 'goodsMovementStore'])->name('store');
            Route::get('/{id}', [SecurityReportController::class, 'goodsMovementShow'])->name('show');
            Route::get('/{id}/edit', [SecurityReportController::class, 'goodsMovementEdit'])->name('edit');
            Route::put('/{id}', [SecurityReportController::class, 'goodsMovementUpdate'])->name('update');
            Route::delete('/{id}', [SecurityReportController::class, 'goodsMovementDestroy'])->name('destroy');
            Route::get('/export/data', [SecurityReportController::class, 'goodsMovementExport'])->name('export');
            Route::get('/export/excel', [SecurityReportController::class, 'goodsMovementExportExcel'])->name('export-excel');
            Route::get('/dashboard/summary', [SecurityReportController::class, 'goodsMovementDashboard'])->name('dashboard');
            Route::get('/dashboard-data', [SecurityReportController::class, 'goodsMovementDashboardData'])->name('dashboard-data');
        });

        // Daily Activity Log Routes
        Route::prefix('daily-activity')->name('daily-activity.')->group(function () {
            Route::get('/', [SecurityReportController::class, 'dailyActivityIndex'])->name('index');
            Route::get('/create', [SecurityReportController::class, 'dailyActivityCreate'])->name('create');
            Route::post('/', [SecurityReportController::class, 'dailyActivityStore'])->name('store');
            Route::get('/export', [SecurityReportController::class, 'dailyActivityExport'])->name('export');
            Route::get('/export/{id}', [SecurityReportController::class, 'dailyActivityExport'])->name('export.single');
            Route::get('/{id}', [SecurityReportController::class, 'dailyActivityShow'])->name('show');
            Route::get('/{id}/edit', [SecurityReportController::class, 'dailyActivityEdit'])->name('edit');
            Route::put('/{id}', [SecurityReportController::class, 'dailyActivityUpdate'])->name('update');
            Route::delete('/{id}', [SecurityReportController::class, 'dailyActivityDestroy'])->name('destroy');
        });
    });

    // Paper Procurement Routes
    Route::prefix('paper-procurement')->name('paper-procurement.')->group(function () {
        Route::get('/', [App\Http\Controllers\PaperProcurementController::class, 'index'])->name('index');
        Route::get('/list', [App\Http\Controllers\PaperProcurementController::class, 'list'])->name('list');
        Route::get('/create', [App\Http\Controllers\PaperProcurementController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\PaperProcurementController::class, 'store'])->name('store');
        Route::get('/search-material', [App\Http\Controllers\PaperProcurementController::class, 'searchMaterial'])->name('search-material');
        Route::get('/search-paper', [App\Http\Controllers\PaperProcurementController::class, 'searchPaper'])->name('search-paper');
        Route::get('/check-stock', [App\Http\Controllers\PaperProcurementController::class, 'checkStock'])->name('check-stock');
        Route::post('/execute-stock-query', [App\Http\Controllers\PaperProcurementController::class, 'executeStockQuery'])->name('execute-stock-query');
        Route::get('/api/locations', [App\Http\Controllers\PaperProcurementController::class, 'getLocations'])->name('api.locations');
        Route::get('/api/paper-stock', [App\Http\Controllers\PaperProcurementController::class, 'getPaperStockByLocation'])->name('api.paper-stock');
        Route::post('/api/po-remain', [App\Http\Controllers\PaperProcurementController::class, 'getPORemain'])->name('api.po-remain');
        Route::get('/{id}/edit', [App\Http\Controllers\PaperProcurementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\PaperProcurementController::class, 'update'])->name('update');
        Route::get('/{id}/excel', [App\Http\Controllers\PaperProcurementController::class, 'showExcel'])->name('show-excel');
        Route::get('/{id}', [App\Http\Controllers\PaperProcurementController::class, 'show'])->name('show');
    });

    // Order Fukumi Routes
    Route::prefix('order-fukumi')->name('order-fukumi.')->group(function () {
        Route::get('/', [App\Http\Controllers\OrderFukumiController::class, 'index'])->name('index');
        Route::post('/generate', [App\Http\Controllers\OrderFukumiController::class, 'generateCodes'])->name('generate');
        Route::post('/generate-export', [App\Http\Controllers\OrderFukumiController::class, 'generateAndExport'])->name('generate-export');
        Route::post('/export', [App\Http\Controllers\OrderFukumiController::class, 'exportToExcel'])->name('export');
    });

    // Label Management Routes
    Route::prefix('label-management')->name('label-management.')->group(function () {
        Route::get('/', [App\Http\Controllers\LabelManagementController::class, 'index'])->name('index');

        // Customer Routes
        Route::get('/customer/create', [App\Http\Controllers\LabelManagementController::class, 'createCustomer'])->name('customer.create');
        Route::post('/customer/store', [App\Http\Controllers\LabelManagementController::class, 'storeCustomer'])->name('customer.store');
        Route::get('/customer/{id}', [App\Http\Controllers\LabelManagementController::class, 'showCustomer'])->name('customer.show');
        Route::get('/customer/{id}/edit', [App\Http\Controllers\LabelManagementController::class, 'editCustomer'])->name('customer.edit');
        Route::put('/customer/{id}', [App\Http\Controllers\LabelManagementController::class, 'updateCustomer'])->name('customer.update');
        Route::delete('/customer/{id}', [App\Http\Controllers\LabelManagementController::class, 'destroyCustomer'])->name('customer.destroy');

        // Template Routes
        Route::get('/customer/{customerId}/template/create', [App\Http\Controllers\LabelManagementController::class, 'createTemplate'])->name('template.create');
        Route::post('/customer/{customerId}/template/store', [App\Http\Controllers\LabelManagementController::class, 'storeTemplate'])->name('template.store');
        Route::get('/template/{id}/edit', [App\Http\Controllers\LabelManagementController::class, 'editTemplate'])->name('template.edit');
        Route::get('/template/{id}/workspace', [App\Http\Controllers\LabelManagementController::class, 'showWorkspace'])->name('template.workspace');
        Route::post('/template/{id}/save-workspace', [App\Http\Controllers\LabelManagementController::class, 'saveWorkspace'])->name('template.save-workspace');
        Route::post('/template/{id}/import-example', [App\Http\Controllers\LabelManagementController::class, 'importExample'])->name('template.import-example');
        Route::post('/template/{id}/convert-excel', [App\Http\Controllers\LabelManagementController::class, 'convertExcelToHtml'])->name('template.convert-excel');
        Route::post('/customer/{customerId}/template/quick-create', [App\Http\Controllers\LabelManagementController::class, 'quickCreateTemplate'])->name('template.quick-create');
        Route::post('/template/{id}/update-field-mapping', [App\Http\Controllers\LabelManagementController::class, 'updateFieldMapping'])->name('template.update-field-mapping');
        Route::get('/template/{id}/preview', [App\Http\Controllers\LabelManagementController::class, 'previewTemplate'])->name('template.preview');
        Route::get('/generation/{generationId}/preview', [App\Http\Controllers\LabelManagementController::class, 'previewTemplate'])->name('generation.preview');
        Route::get('/template/{id}/generate', [App\Http\Controllers\LabelManagementController::class, 'showGenerateForm'])->name('template.generate-form');
        Route::post('/template/{id}/generate', [App\Http\Controllers\LabelManagementController::class, 'generateLabel'])->name('template.generate');
        Route::get('/template/{id}', [App\Http\Controllers\LabelManagementController::class, 'showTemplate'])->name('template.show');
        Route::put('/template/{id}', [App\Http\Controllers\LabelManagementController::class, 'updateTemplate'])->name('template.update');
        Route::delete('/template/{id}', [App\Http\Controllers\LabelManagementController::class, 'destroyTemplate'])->name('template.destroy');

        // Master Customer Search
        Route::get('/search-master-customer', [App\Http\Controllers\LabelManagementController::class, 'searchMasterCustomer'])->name('search-master-customer');
        Route::get('/master-customer/{code}', [App\Http\Controllers\LabelManagementController::class, 'getMasterCustomer'])->name('master-customer.get');

        // Work Order Search
        Route::get('/search-work-order', [App\Http\Controllers\LabelManagementController::class, 'searchWorkOrder'])->name('search-work-order');
        Route::get('/work-order/{wot}', [App\Http\Controllers\LabelManagementController::class, 'getWorkOrder'])->name('work-order.get');

        // Master Item Unilever
        Route::get('/master-item-unilever', [App\Http\Controllers\LabelManagementController::class, 'masterItemUnileverIndex'])->name('master-item-unilever.index');
        Route::post('/master-item-unilever', [App\Http\Controllers\LabelManagementController::class, 'storeMasterItemUnilever'])->name('master-item-unilever.store');
        Route::put('/master-item-unilever/{id}', [App\Http\Controllers\LabelManagementController::class, 'updateMasterItemUnilever'])->name('master-item-unilever.update');
        Route::delete('/master-item-unilever/{id}', [App\Http\Controllers\LabelManagementController::class, 'destroyMasterItemUnilever'])->name('master-item-unilever.destroy');
        Route::get('/search-master-material', [App\Http\Controllers\LabelManagementController::class, 'searchMasterMaterial'])->name('search-master-material');

        // Master Code Operator
        Route::get('/master-code-operator', [App\Http\Controllers\LabelManagementController::class, 'masterCodeOperatorIndex'])->name('master-code-operator.index');
        Route::post('/master-code-operator', [App\Http\Controllers\LabelManagementController::class, 'storeMasterCodeOperator'])->name('master-code-operator.store');
        Route::put('/master-code-operator/{id}', [App\Http\Controllers\LabelManagementController::class, 'updateMasterCodeOperator'])->name('master-code-operator.update');
        Route::delete('/master-code-operator/{id}', [App\Http\Controllers\LabelManagementController::class, 'destroyMasterCodeOperator'])->name('master-code-operator.destroy');
        Route::post('/generate', [App\Http\Controllers\LabelManagementController::class, 'generate'])->name('generate');
        Route::post('/export', [App\Http\Controllers\LabelManagementController::class, 'export'])->name('export');
    });

    // API untuk mendapatkan locations dari masterlocation
    Route::get('/api/locations', [App\Http\Controllers\PaperProcurementController::class, 'getLocations'])->name('api.locations');


    Route::get('forecasting', [ForecastingController::class, 'index'])->name('forecasting.index');
    Route::get('forecasting/list', [ForecastingController::class, 'list'])->name('forecasting.list');
    Route::get('forecasting/create', [ForecastingController::class, 'create'])->name('forecasting.create');
    Route::post('forecasting', [ForecastingController::class, 'store'])->name('forecasting.store');
    Route::get('forecasting/{id}/edit', [ForecastingController::class, 'edit'])->name('forecasting.edit');
    Route::put('forecasting/{id}', [ForecastingController::class, 'update'])->name('forecasting.update');
    Route::delete('forecasting/{id}', [ForecastingController::class, 'destroy'])->name('forecasting.destroy');
    Route::post('forecasting/data', [ForecastingController::class, 'data'])->name('forecasting.data');
    Route::post('forecasting/preview-import', [ForecastingController::class, 'previewImport'])->name('forecasting.preview-import');
    Route::post('forecasting/confirm-import', [ForecastingController::class, 'confirmImport'])->name('forecasting.confirm-import');
    Route::post('forecasting/import', [ForecastingController::class, 'import'])->name('forecasting.import'); // Deprecated
    Route::get('forecasting/download-template', [ForecastingController::class, 'downloadTemplate'])->name('forecasting.download-template');


    // Ebook PKB

    require __DIR__ . '/auth.php';
});


Route::post('/plan/save', [ProcessController::class, 'savePlanFromPreview'])->name('plan.save');
// Route::prefix('ebook-pkb')->name('ebook-pkb.')->group(function () {
// Route::get('sipo/ebook-pkb', [EbookPKBController::class, 'index'])->name('ebook-pkb.index');
// Route::get('sipo/ebook-pkb/search', [EbookPKBController::class, 'search'])->name('ebook-pkb.search');
// });

// Forecasting Routes
