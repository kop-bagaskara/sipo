<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PpicPaperMeeting;
use App\Models\PpicPaperMeetingItem;
use App\Models\PpicPaperMeetingPaper;
use App\Models\PpicPaperMeetingLocation;
use App\Models\PpicPaperMeetingStock;
use App\Models\PpicPaperMeetingPORemain;
use App\Models\PpicPaperMeetingPOManual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\POService;

class PaperProcurementController extends Controller
{
    /**
     * Display paper procurement index page dengan tabel pengajuan
     */
    public function index()
    {
        // Ambil semua meeting dengan relasi creator
        $meetings = PpicPaperMeeting::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main.paper-procurement.index', compact('meetings'));
    }

    /**
     * Display paper procurement form for specific month and customer group
     */
    public function create(Request $request)
    {
        $month = $request->get('month'); // Bulan pertama periode (contoh: OCT untuk OKTOBER)
        $customerGroup = $request->get('customer_group');
        $template = $request->get('template', 'template1'); // Default template1 jika tidak dipilih

        // Generate 3-month period based on selected month (bulan yang dipilih + 2 bulan berikutnya)
        $months = $this->generateThreeMonthPeriod($month);

        // Get month keys and names for display
        $allMonths = [
            'JAN' => 'Januari',
            'FEB' => 'Februari',
            'MAR' => 'Maret',
            'APR' => 'April',
            'MAY' => 'Mei',
            'JUN' => 'Juni',
            'JUL' => 'Juli',
            'AUG' => 'Agustus',
            'SEP' => 'September',
            'OCT' => 'Oktober',
            'NOV' => 'November',
            'DEC' => 'Desember',
        ];

        $monthKeys = array_keys($allMonths);
        $currentIndex = array_search($month, $monthKeys);

        // Calculate 3 months period
        $periodMonth1 = $month;
        $periodMonth2 = $monthKeys[($currentIndex + 1) % 12];
        $periodMonth3 = $monthKeys[($currentIndex + 2) % 12];

        // Meeting month is the first month of period
        $meetingMonth = $allMonths[$month] . ' ' . date('Y');

        // Get products and papers based on customer group
        $productCategories = $this->getProductCategoriesByCustomer($customerGroup);
        $paperTypes = $this->getPaperTypesByCustomer($customerGroup);

        // Get locations from masterlocation table
        $locations = DB::connection('mysql3')
            ->table('masterlocation')
            // ->where('is_active', 1)
            ->orderBy('Code')
            ->select('Code', 'Name')
            ->get();

        // Determine which view to return based on template selection
        $viewName = ($template === 'template2')
            ? 'main.paper-procurement.create-paper-template2'
            : 'main.paper-procurement.create-v2';

        return view($viewName, compact(
            'month',
            'customerGroup',
            'months',
            'allMonths',
            'periodMonth1',
            'periodMonth2',
            'periodMonth3',
            'meetingMonth',
            'productCategories',
            'paperTypes',
            'locations',
            'template'
        ))->with('productCategoriesJson', json_encode($productCategories));
    }

    /**
     * Get product categories and products by customer
     */
    private function getProductCategoriesByCustomer($customerGroup)
    {
        // Data produk berdasarkan customer group
        // Untuk TSPM
        if ($customerGroup === 'TSPM') {
            return [
                [
                    'name' => 'Carton Juara',
                    'color' => '#ffffff',
                    'border_color' => '#dee2e6',
                    'products' => [
                        'Carton Juara Teh Manis',
                        'Carton Juara Ja\'abu',
                        'Carton Juara Mangga',
                        'Carton Juara Berry',
                        'Carton Juara Pisang',
                        'Carton Juara Cokelat',
                    ],
                    'paper_types' => ['DPC 250'] // Kolom L
                ],
                [
                    'name' => 'Pack Packaging Juara',
                    'color' => '#d1fae5', // Light green
                    'border_color' => '#10b981',
                    'products' => [
                        'Pack Packaging Juara Teh Manis',
                        'Pack Packaging Juara Ja\'abu',
                        'Pack Packaging Juara mangga',
                        'Pack Packaging Juara berry',
                        'Pack Packaging Juara pisang',
                        'Pack Packaging Juara cokelat',
                    ],
                    'paper_types' => ['IVORY 230 IKDP VR', 'IVORY 230 SPN'] // Kolom Q dan R
                ],
                [
                    'name' => 'Inner Frame Juara',
                    'color' => '#fef3c7', // Light orange
                    'border_color' => '#f59e0b',
                    'products' => [
                        'Inner Frame Juara Teh Manis',
                        'Inner Frame Juara Ja\'abu',
                        'Inner Frame Juara Mangga',
                        'Inner Frame Juara Berry',
                        'Inner Frame Juara pisang',
                        'Inner Frame Juara cokelat',
                    ],
                    'paper_types' => ['IVORY 230 IK VR'] // Kolom U
                ],
                [
                    'name' => 'Esse Cigar / Esse India',
                    'color' => '#dbeafe', // Light blue
                    'border_color' => '#3b82f6',
                    'products' => [
                        'Esse Cigar Cacao',
                        'Esse Cigar Purple',
                        'Esse India Black',
                        'Esse India Change Blue',
                        'EsseIndia Change Juicy',
                        'Esse Change Double Kedara',
                        'Pack Packaging Prime',
                    ],
                    'paper_types' => ['IVORY SINAR VANDA 220'] // Kolom V
                ],
            ];
        }

        // Default untuk customer lain (bisa dikembangkan)
        return [];
    }

    /**
     * Get paper types configuration by customer
     */
    private function getPaperTypesByCustomer($customerGroup)
    {
        if ($customerGroup === 'TSPM') {
            return [
                'DPC 250' => [
                    [
                        'size' => '(73 x 52) @4 up (IKDP)',
                        'code' => 'K.060.0250.PLN.087',
                        'column' => 'L'
                    ]
                ],
                'IVORY 230 IKDP VR' => [
                    [
                        'size' => '(62.5 x 94) @ 30 up (IKDP VR)',
                        'code' => 'K.040.0230.PLN.062',
                        'column' => 'Q'
                    ]
                ],
                'IVORY 230 SPN' => [
                    [
                        'size' => '(62.5 x 94) @ 30 up (SPN)',
                        'code' => 'K.040.0230.PLN.065',
                        'column' => 'R'
                    ]
                ],
                'IVORY 230 IK VR' => [
                    [
                        'size' => '(61 x 97.5) @ 63 up (IK VR)',
                        'code' => 'K.040.0230.PLN.061',
                        'column' => 'U'
                    ]
                ],
                'IVORY SINAR VANDA 220' => [
                    [
                        'size' => '(100.5 x 64) @ 32 up',
                        'code' => 'K.040.0220.PLN.001',
                        'column' => 'V'
                    ]
                ],
            ];
        }

        return [];
    }

    /**
     * Display list of paper procurement requests (alias untuk index)
     */
    public function list()
    {
        return $this->index();
    }

    /**
     * Store new paper meeting request
     */
    public function store(Request $request)
    {

        // Validasi input
        $validated = $request->validate([
            'meeting_month' => 'required|string',
            // 'customer_name' => 'required|string|in:TSPM,UNILEVER,NABATI,OTHERS,VDR',
            // 'period_month_1' => 'required|string',
            // 'period_month_2' => 'required|string',
            // 'period_month_3' => 'required|string',
            // 'tolerance_percentage' => 'nullable|numeric|min:0|max:100',
            // 'items' => 'required|array|min:1',
            // 'items.*.qty_month_1' => 'required|integer|min:0',
            // 'items.*.qty_month_2' => 'required|integer|min:0',
            // 'items.*.qty_month_3' => 'required|integer|min:0',
            // 'items.*.papers' => 'nullable|array',
            // 'locations' => 'nullable|array',
            // 'stocks' => 'nullable|array',
            // 'po_remains' => 'nullable|array',
            // 'po_manuals' => 'nullable|array',
            // 'paper_calculations' => 'nullable|array',
            // 'locations_json' => 'nullable|string',
            // 'stocks_json' => 'nullable|string',
            // 'po_remains_json' => 'nullable|string',
            // 'po_manuals_json' => 'nullable|string',
            // 'paper_calculations_json' => 'nullable|string',
        ]);

        // dd($request->all());

        DB::beginTransaction();
        try {
            // Generate meeting number
            $meetingNumber = PpicPaperMeeting::generateMeetingNumber();
            // dd($meetingNumber);

            // Simpan meeting
            $meeting = PpicPaperMeeting::create([
                'meeting_number' => $meetingNumber,
                'customer_name' => $request->customer_name,
                'meeting_month' => $request->meeting_month,
                'period_month_1' => $request->period_month_1,
                'period_month_2' => $request->period_month_2,
                'period_month_3' => $request->period_month_3,
                'tolerance_percentage' => $request->tolerance_percentage ?? 10.00,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // dd($meeting);

            // Simpan items dan papers
            $sortOrder = 1;
            foreach ($request->items as $itemId => $itemData) {
                // Hitung total quantity
                $qty1 = (int) ($itemData['qty_month_1'] ?? 0);
                $qty2 = (int) ($itemData['qty_month_2'] ?? 0);
                $qty3 = (int) ($itemData['qty_month_3'] ?? 0);
                $totalQuantity = $qty1 + $qty2 + $qty3;
                $totalWithTolerance = (int) ceil($totalQuantity * (1 + ($meeting->tolerance_percentage / 100)));

                // Tentukan product category dari product name (jika ada)
                $productName = $itemData['product_name'] ?? '';
                $productCode = $itemData['product_code'] ?? null;
                $productCategory = $this->determineProductCategory($productName, $meeting->customer_name);

                // Simpan item
                $meetingItem = $meeting->items()->create([
                    'product_code' => $productCode,
                    'product_name' => $productName,
                    'product_category' => $productCategory,
                    'quantity_month_1' => $qty1,
                    'quantity_month_2' => $qty2,
                    'quantity_month_3' => $qty3,
                    'total_quantity' => $totalQuantity,
                    'total_with_tolerance' => $totalWithTolerance,
                    'sort_order' => $sortOrder++,
                ]);

                // Simpan papers untuk item ini
                if (isset($itemData['papers']) && is_array($itemData['papers'])) {
                    foreach ($itemData['papers'] as $paperCode => $paperData) {
                        if (isset($paperData['quantity']) && $paperData['quantity'] > 0) {
                            $meetingItem->papers()->create([
                                'paper_type' => $paperData['paper_type'] ?? null,
                                'paper_code' => $paperData['paper_code'] ?? $paperCode,
                                'paper_name' => $paperData['paper_name'] ?? null,
                                'paper_size' => $paperData['paper_size'] ?? null,
                                'paper_variant' => null,
                                'up_count' => null,
                                'required_quantity' => (int) $paperData['quantity'],
                            ]);
                        }
                    }
                }
            }

            // Simpan locations (dari JSON atau array)
            $locationsData = [];
            if ($request->has('locations_json')) {
                $locationsData = json_decode($request->input('locations_json'), true) ?? [];
            } elseif ($request->has('locations') && is_array($request->locations)) {
                $locationsData = $request->locations;
            }

            if (!empty($locationsData)) {
                $locationSortOrder = 1;
                foreach ($locationsData as $locationData) {
                    $meeting->locations()->create([
                        'location_code' => $locationData['code'] ?? $locationData['Code'] ?? null,
                        'location_name' => $locationData['name'] ?? $locationData['Name'] ?? null,
                        'sort_order' => $locationSortOrder++,
                    ]);
                }
            }

            // Simpan stocks per location (dari JSON atau array)
            $stocksData = [];
            if ($request->has('stocks_json')) {
                $stocksData = json_decode($request->input('stocks_json'), true) ?? [];
            } elseif ($request->has('stocks') && is_array($request->stocks)) {
                $stocksData = $request->stocks;
            }

            if (!empty($stocksData)) {
                foreach ($stocksData as $stockData) {
                    $locationCode = $stockData['location_code'] ?? null;
                    if ($locationCode) {
                        $location = $meeting->locations()->where('location_code', $locationCode)->first();
                        if ($location) {
                            foreach ($stockData['papers'] ?? [] as $paperCode => $paperStockData) {
                                $meeting->stocks()->create([
                                    'location_id' => $location->id,
                                    'paper_code' => is_string($paperStockData) ? $paperCode : ($paperStockData['paper_code'] ?? $paperCode),
                                    'paper_type' => is_array($paperStockData) ? ($paperStockData['paper_type'] ?? null) : null,
                                    'stock_layer_1' => is_array($paperStockData) ? (float) ($paperStockData['stock_layer_1'] ?? 0) : 0,
                                    'stock_layer_2' => is_array($paperStockData) ? (float) ($paperStockData['stock_layer_2'] ?? 0) : 0,
                                    'stock_layer_3' => is_array($paperStockData) ? (float) ($paperStockData['stock_layer_3'] ?? 0) : 0,
                                ]);
                            }
                        }
                    }
                }
            }

            // Simpan PO Remains (dari JSON atau array)
            $poRemainsData = [];
            if ($request->has('po_remains_json')) {
                $poRemainsData = json_decode($request->input('po_remains_json'), true) ?? [];
            } elseif ($request->has('po_remains') && is_array($request->po_remains)) {
                $poRemainsData = $request->po_remains;
            }

            if (!empty($poRemainsData)) {
                foreach ($poRemainsData as $poRemainData) {
                    $meeting->poRemains()->create([
                        'po_doc_no' => $poRemainData['po_doc_no'] ?? $poRemainData['doc_no'] ?? $poRemainData['DocNo'] ?? null,
                        'paper_code' => $poRemainData['paper_code'] ?? null,
                        'paper_type' => $poRemainData['paper_type'] ?? null,
                        'qty_remain' => (float) ($poRemainData['qty_remain'] ?? 0),
                        'po_remain_layer_1' => (float) ($poRemainData['po_remain_layer_1'] ?? 0),
                        'po_remain_layer_2' => (float) ($poRemainData['po_remain_layer_2'] ?? 0),
                        'up_value' => (float) ($poRemainData['up_value'] ?? 5),
                    ]);
                }
            }

            // Simpan PO Manuals (BELUM ADA PO) (dari JSON atau array)
            $poManualsData = [];
            if ($request->has('po_manuals_json')) {
                $poManualsData = json_decode($request->input('po_manuals_json'), true) ?? [];
            } elseif ($request->has('po_manuals') && is_array($request->po_manuals)) {
                $poManualsData = $request->po_manuals;
            }

            if (!empty($poManualsData)) {
                foreach ($poManualsData as $paperCode => $poManualData) {
                    $meeting->poManuals()->updateOrCreate(
                        [
                            'meeting_id' => $meeting->id,
                            'paper_code' => is_string($poManualData) ? $paperCode : ($poManualData['paper_code'] ?? $paperCode),
                        ],
                        [
                            'paper_type' => is_array($poManualData) ? ($poManualData['paper_type'] ?? null) : null,
                            'po_manual_layer_1' => is_array($poManualData) ? (float) ($poManualData['po_manual_layer_1'] ?? 0) : 0,
                            'po_manual_layer_2' => is_array($poManualData) ? (float) ($poManualData['po_manual_layer_2'] ?? 0) : 0,
                            'up_value' => is_array($poManualData) ? (float) ($poManualData['up_value'] ?? 5) : 5,
                        ]
                    );
                }
            }

            // Simpan perhitungan per jenis kertas (MINUS PAPER, TOTAL KEBUTUHAN, dll) (dari JSON atau array)
            $paperCalculationsData = [];
            if ($request->has('paper_calculations_json')) {
                $paperCalculationsData = json_decode($request->input('paper_calculations_json'), true) ?? [];
            } elseif ($request->has('paper_calculations') && is_array($request->paper_calculations)) {
                $paperCalculationsData = $request->paper_calculations;
            }

            if (!empty($paperCalculationsData)) {
                foreach ($paperCalculationsData as $paperType => $calcData) {
                    // Update semua paper dengan paper_type yang sama
                    $papers = PpicPaperMeetingPaper::whereHas('meetingItem', function($query) use ($meeting) {
                        $query->where('meeting_id', $meeting->id);
                    })->where('paper_type', $paperType)->get();

                    foreach ($papers as $paper) {
                        $paper->update([
                            'paper_name' => $calcData['paper_name'] ?? $paper->paper_name,
                            'up_value' => (float) ($calcData['up_value'] ?? 5),
                            'zgsm' => (float) ($calcData['zgsm'] ?? 0),
                            'zlength' => (float) ($calcData['zlength'] ?? 0),
                            'zwidth' => (float) ($calcData['zwidth'] ?? 0),
                            'cover_sampai' => $calcData['cover_sampai'] ?? null,
                            'minus_paper_pcs' => (float) ($calcData['minus_paper_pcs'] ?? 0),
                            'minus_paper_rim' => (float) ($calcData['minus_paper_rim'] ?? 0),
                            'minus_paper_ton' => (float) ($calcData['minus_paper_ton'] ?? 0),
                            'total_kebutuhan_ton' => (float) ($calcData['total_kebutuhan_ton'] ?? 0),
                            'catatan' => $calcData['catatan'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('paper-procurement.index')
                ->with('success', "Pengajuan meeting kertas berhasil dibuat dengan nomor: {$meetingNumber}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing paper meeting:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Determine product category based on product name and customer
     */
    private function determineProductCategory($productName, $customerGroup)
    {
        if ($customerGroup === 'TSPM') {
            $productNameLower = strtolower($productName);

            if (strpos($productNameLower, 'carton juara') !== false) {
                return 'Carton Juara';
            } elseif (strpos($productNameLower, 'pack packaging juara') !== false) {
                return 'Pack Packaging Juara';
            } elseif (strpos($productNameLower, 'inner frame juara') !== false) {
                return 'Inner Frame Juara';
            } elseif (strpos($productNameLower, 'esse') !== false) {
                return 'Esse Cigar / Esse India';
            }
        }

        return 'Other';
    }

    /**
     * Display paper procurement detail in Excel style
     */
    public function showExcel($id)
    {
        return view('main.paper-procurement.show-excel');
    }

    /**
     * Display paper procurement detail (Excel-like format)
     */
    public function show($id)
    {
        $meeting = PpicPaperMeeting::with([
            'items.papers',
            'locations.stocks',
            'stocks',
            'poRemains',
            'poManuals',
            'creator'
        ])->findOrFail($id);

        return view('main.paper-procurement.show-v2', compact('meeting'));
    }

    /**
     * Show the form for editing the specified paper meeting
     */
    public function edit($id)
    {
        $meeting = PpicPaperMeeting::with([
            'items.papers',
            'locations.stocks',
            'stocks',
            'poRemains',
            'poManuals',
            'creator'
        ])->findOrFail($id);

        // Extract month from meeting_month (format: "Oktober 2025")
        $meetingMonthParts = explode(' ', $meeting->meeting_month);
        $monthName = $meetingMonthParts[0] ?? 'OCT';

        $allMonths = [
            'Januari' => 'JAN',
            'Februari' => 'FEB',
            'Maret' => 'MAR',
            'April' => 'APR',
            'Mei' => 'MAY',
            'Juni' => 'JUN',
            'Juli' => 'JUL',
            'Agustus' => 'AUG',
            'September' => 'SEP',
            'Oktober' => 'OCT',
            'November' => 'NOV',
            'Desember' => 'DEC',
        ];

        $month = $allMonths[$monthName] ?? 'OCT';
        $customerGroup = $meeting->customer_name;

        // Generate 3-month period
        $months = $this->generateThreeMonthPeriod($month);
        $periodMonth1 = $meeting->period_month_1;
        $periodMonth2 = $meeting->period_month_2;
        $periodMonth3 = $meeting->period_month_3;
        $meetingMonth = $meeting->meeting_month;

        // Get products and papers based on customer group
        $productCategories = $this->getProductCategoriesByCustomer($customerGroup);
        $paperTypes = $this->getPaperTypesByCustomer($customerGroup);

        // Get locations from masterlocation table
        $locations = DB::connection('mysql3')
            ->table('masterlocation')
            ->orderBy('Code')
            ->select('Code', 'Name')
            ->get();

        return view('main.paper-procurement.edit-v2', compact(
            'meeting',
            'month',
            'customerGroup',
            'months',
            'allMonths',
            'periodMonth1',
            'periodMonth2',
            'periodMonth3',
            'meetingMonth',
            'productCategories',
            'paperTypes',
            'locations'
        ))->with('productCategoriesJson', json_encode($productCategories));
    }

    /**
     * Update the specified paper meeting
     */
    public function update(Request $request, $id)
    {
        $meeting = PpicPaperMeeting::findOrFail($id);

        // Validasi input (sama seperti store)
        $validated = $request->validate([
            'meeting_month' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Update meeting basic info
            $meeting->update([
                'meeting_month' => $request->meeting_month,
                'customer_name' => $request->customer_name,
                'period_month_1' => $request->period_month_1,
                'period_month_2' => $request->period_month_2,
                'period_month_3' => $request->period_month_3,
                'tolerance_percentage' => $request->tolerance_percentage ?? 10.00,
            ]);

            // Hapus semua data terkait yang lama (untuk re-populate)
            $meeting->items()->delete();
            $meeting->locations()->delete();
            $meeting->stocks()->delete();
            $meeting->poRemains()->delete();
            $meeting->poManuals()->delete();

            // Simpan items dan papers (sama seperti store)
            $sortOrder = 1;
            foreach ($request->items as $itemId => $itemData) {
                $qty1 = (int) ($itemData['qty_month_1'] ?? 0);
                $qty2 = (int) ($itemData['qty_month_2'] ?? 0);
                $qty3 = (int) ($itemData['qty_month_3'] ?? 0);
                $totalQuantity = $qty1 + $qty2 + $qty3;
                $totalWithTolerance = (int) ceil($totalQuantity * (1 + ($meeting->tolerance_percentage / 100)));

                $productName = $itemData['product_name'] ?? '';
                $productCode = $itemData['product_code'] ?? null;
                $productCategory = $this->determineProductCategory($productName, $meeting->customer_name);

                $meetingItem = $meeting->items()->create([
                    'product_code' => $productCode,
                    'product_name' => $productName,
                    'product_category' => $productCategory,
                    'quantity_month_1' => $qty1,
                    'quantity_month_2' => $qty2,
                    'quantity_month_3' => $qty3,
                    'total_quantity' => $totalQuantity,
                    'total_with_tolerance' => $totalWithTolerance,
                    'sort_order' => $sortOrder++,
                ]);

                if (isset($itemData['papers']) && is_array($itemData['papers'])) {
                    foreach ($itemData['papers'] as $paperCode => $paperData) {
                        if (isset($paperData['quantity']) && $paperData['quantity'] > 0) {
                            $meetingItem->papers()->create([
                                'paper_type' => $paperData['paper_type'] ?? null,
                                'paper_code' => $paperData['paper_code'] ?? $paperCode,
                                'paper_name' => $paperData['paper_name'] ?? null,
                                'paper_size' => $paperData['paper_size'] ?? null,
                                'paper_variant' => null,
                                'up_count' => null,
                                'required_quantity' => (int) $paperData['quantity'],
                            ]);
                        }
                    }
                }
            }

            // Simpan locations
            $locationsData = [];
            if ($request->has('locations_json')) {
                $locationsData = json_decode($request->input('locations_json'), true) ?? [];
            } elseif ($request->has('locations') && is_array($request->locations)) {
                $locationsData = $request->locations;
            }

            if (!empty($locationsData)) {
                $locationSortOrder = 1;
                foreach ($locationsData as $locationData) {
                    $meeting->locations()->create([
                        'location_code' => $locationData['code'] ?? $locationData['Code'] ?? null,
                        'location_name' => $locationData['name'] ?? $locationData['Name'] ?? null,
                        'sort_order' => $locationSortOrder++,
                    ]);
                }
            }

            // Simpan stocks per location
            $stocksData = [];
            if ($request->has('stocks_json')) {
                $stocksData = json_decode($request->input('stocks_json'), true) ?? [];
            } elseif ($request->has('stocks') && is_array($request->stocks)) {
                $stocksData = $request->stocks;
            }

            if (!empty($stocksData)) {
                foreach ($stocksData as $stockData) {
                    $locationCode = $stockData['location_code'] ?? null;
                    if ($locationCode) {
                        $location = $meeting->locations()->where('location_code', $locationCode)->first();
                        if ($location) {
                            foreach ($stockData['papers'] ?? [] as $paperCode => $paperStockData) {
                                $meeting->stocks()->create([
                                    'location_id' => $location->id,
                                    'paper_code' => is_string($paperStockData) ? $paperCode : ($paperStockData['paper_code'] ?? $paperCode),
                                    'paper_type' => is_array($paperStockData) ? ($paperStockData['paper_type'] ?? null) : null,
                                    'stock_layer_1' => is_array($paperStockData) ? (float) ($paperStockData['stock_layer_1'] ?? 0) : 0,
                                    'stock_layer_2' => is_array($paperStockData) ? (float) ($paperStockData['stock_layer_2'] ?? 0) : 0,
                                    'stock_layer_3' => is_array($paperStockData) ? (float) ($paperStockData['stock_layer_3'] ?? 0) : 0,
                                ]);
                            }
                        }
                    }
                }
            }

            // Simpan PO Remains
            $poRemainsData = [];
            if ($request->has('po_remains_json')) {
                $poRemainsData = json_decode($request->input('po_remains_json'), true) ?? [];
            } elseif ($request->has('po_remains') && is_array($request->po_remains)) {
                $poRemainsData = $request->po_remains;
            }

            if (!empty($poRemainsData)) {
                foreach ($poRemainsData as $poRemainData) {
                    $meeting->poRemains()->create([
                        'po_doc_no' => $poRemainData['po_doc_no'] ?? $poRemainData['doc_no'] ?? $poRemainData['DocNo'] ?? null,
                        'paper_code' => $poRemainData['paper_code'] ?? null,
                        'paper_type' => $poRemainData['paper_type'] ?? null,
                        'qty_remain' => (float) ($poRemainData['qty_remain'] ?? 0),
                        'po_remain_layer_1' => (float) ($poRemainData['po_remain_layer_1'] ?? 0),
                        'po_remain_layer_2' => (float) ($poRemainData['po_remain_layer_2'] ?? 0),
                        'up_value' => (float) ($poRemainData['up_value'] ?? 5),
                    ]);
                }
            }

            // Simpan PO Manuals
            $poManualsData = [];
            if ($request->has('po_manuals_json')) {
                $poManualsData = json_decode($request->input('po_manuals_json'), true) ?? [];
            } elseif ($request->has('po_manuals') && is_array($request->po_manuals)) {
                $poManualsData = $request->po_manuals;
            }

            if (!empty($poManualsData)) {
                foreach ($poManualsData as $paperCode => $poManualData) {
                    $meeting->poManuals()->updateOrCreate(
                        [
                            'meeting_id' => $meeting->id,
                            'paper_code' => is_string($poManualData) ? $paperCode : ($poManualData['paper_code'] ?? $paperCode),
                        ],
                        [
                            'paper_type' => is_array($poManualData) ? ($poManualData['paper_type'] ?? null) : null,
                            'po_manual_layer_1' => is_array($poManualData) ? (float) ($poManualData['po_manual_layer_1'] ?? 0) : 0,
                            'po_manual_layer_2' => is_array($poManualData) ? (float) ($poManualData['po_manual_layer_2'] ?? 0) : 0,
                            'up_value' => is_array($poManualData) ? (float) ($poManualData['up_value'] ?? 5) : 5,
                        ]
                    );
                }
            }

            // Update perhitungan per jenis kertas
            $paperCalculationsData = [];
            if ($request->has('paper_calculations_json')) {
                $paperCalculationsData = json_decode($request->input('paper_calculations_json'), true) ?? [];
            } elseif ($request->has('paper_calculations') && is_array($request->paper_calculations)) {
                $paperCalculationsData = $request->paper_calculations;
            }

            if (!empty($paperCalculationsData)) {
                foreach ($paperCalculationsData as $paperType => $calcData) {
                    $papers = PpicPaperMeetingPaper::whereHas('meetingItem', function($query) use ($meeting) {
                        $query->where('meeting_id', $meeting->id);
                    })->where('paper_type', $paperType)->get();

                    foreach ($papers as $paper) {
                        $paper->update([
                            'paper_name' => $calcData['paper_name'] ?? $paper->paper_name,
                            'up_value' => (float) ($calcData['up_value'] ?? 5),
                            'zgsm' => (float) ($calcData['zgsm'] ?? 0),
                            'zlength' => (float) ($calcData['zlength'] ?? 0),
                            'zwidth' => (float) ($calcData['zwidth'] ?? 0),
                            'cover_sampai' => $calcData['cover_sampai'] ?? null,
                            'minus_paper_pcs' => (float) ($calcData['minus_paper_pcs'] ?? 0),
                            'minus_paper_rim' => (float) ($calcData['minus_paper_rim'] ?? 0),
                            'minus_paper_ton' => (float) ($calcData['minus_paper_ton'] ?? 0),
                            'total_kebutuhan_ton' => (float) ($calcData['total_kebutuhan_ton'] ?? 0),
                            'catatan' => $calcData['catatan'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('paper-procurement.show', $meeting->id)
                ->with('success', "Pengajuan meeting kertas berhasil diupdate.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating paper meeting:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate pengajuan. Silakan coba lagi atau hubungi administrator.');
        }
    }

    /**
     * Generate 3-month period based on selected month
     */
    private function generateThreeMonthPeriod($selectedMonth)
    {
        $allMonths = [
            'JAN' => 'Januari',
            'FEB' => 'Februari',
            'MAR' => 'Maret',
            'APR' => 'April',
            'MAY' => 'Mei',
            'JUN' => 'Juni',
            'JUL' => 'Juli',
            'AUG' => 'Agustus',
            'SEP' => 'September',
            'OCT' => 'Oktober',
            'NOV' => 'November',
            'DEC' => 'Desember',
        ];

        $monthKeys = array_keys($allMonths);
        $currentIndex = array_search($selectedMonth, $monthKeys);

        $threeMonths = [];
        for ($i = 0; $i < 3; $i++) {
            $index = ($currentIndex + $i) % 12;
            $key = $monthKeys[$index];
            $threeMonths[$key] = $allMonths[$key];
        }

        return $threeMonths;
    }

    /**
     * Search material from mastermaterial table (mysql3)
     */
    public function searchMaterial(Request $request)
    {
        $search = $request->get('q', '');

        if (empty($search)) {
            return response()->json([]);
        }

        try {
            $materials = DB::connection('mysql3')
                ->table('mastermaterial')
                ->where('Code', 'like', '%' . $search . '%')
                ->orWhere('Name', 'like', '%' . $search . '%')
                ->select('Code', 'Name')
                ->limit(50)
                ->get();

            $results = $materials->map(function ($material) {
                return [
                    'id' => $material->Code,
                    'code' => $material->Code,
                    'name' => $material->Name,
                    'text' => $material->Code . ' - ' . $material->Name // Format untuk Select2
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error searching material: ' . $e->getMessage());
            return response()->json(['error' => 'Error searching material'], 500);
        }
    }

    /**
     * Search paper from mastermaterial table (mysql3) - Code starts with "K."
     */
    public function searchPaper(Request $request)
    {
        $search = $request->get('q', '');

        if (empty($search)) {
            return response()->json([]);
        }

        try {
            $papers = DB::connection('mysql3')
                ->table('mastermaterial')
                ->where('Code', 'like', 'K.%') // Code selalu diawali K.
                ->where(function($query) use ($search) {
                    $query->where('Code', 'like', '%' . $search . '%')
                          ->orWhere('Name', 'like', '%' . $search . '%');
                })
                ->select('Code', 'Name', 'ZGSM', 'ZLength', 'ZWidth')
                ->limit(50)
                ->get();

            // Mapping Code ke warna
            $colorMapping = [
                'K.060.0250' => '#fff2cc', // DPC 250 - Kuning/cream
                'K.040.0230.PLN.062' => '#d5e8d4', // IVORY 230 IKDP VR - Hijau muda
                'K.040.0230.PLN.065' => '#d5e8d4', // IVORY 230 SPN - Hijau muda
                'K.040.0230.PLN.061' => '#ffe6cc', // IVORY 230 IK VR - Orange/peach
                'K.040.0220.PLN.001' => '#dbeafe', // IVORY SINAR VANDA 220 - Biru muda
            ];

            // Mapping Code ke paperType
            $paperTypeMapping = [
                'K.060.0250' => 'DPC 250',
                'K.040.0230.PLN.062' => 'IVORY 230 IKDP VR',
                'K.040.0230.PLN.065' => 'IVORY 230 SPN',
                'K.040.0230.PLN.061' => 'IVORY 230 IK VR',
                'K.040.0220.PLN.001' => 'IVORY SINAR VANDA 220',
            ];

            $results = $papers->map(function ($paper) use ($colorMapping, $paperTypeMapping) {
                $code = $paper->Code;
                $color = '#ffffff'; // Default putih
                $paperType = null;

                // Cari warna berdasarkan Code (exact match atau partial match)
                foreach ($colorMapping as $pattern => $mappedColor) {
                    if (strpos($code, $pattern) !== false || strpos($code, str_replace('K.', '', $pattern)) !== false) {
                        $color = $mappedColor;
                        break;
                    }
                }

                // Cari paperType berdasarkan Code
                foreach ($paperTypeMapping as $pattern => $mappedType) {
                    if (strpos($code, $pattern) !== false || strpos($code, str_replace('K.', '', $pattern)) !== false) {
                        $paperType = $mappedType;
                        break;
                    }
                }

                // Jika tidak ditemukan, coba deteksi dari Name
                if (!$paperType) {
                    $nameUpper = strtoupper($paper->Name);
                    if (strpos($nameUpper, 'DPC 250') !== false || strpos($nameUpper, 'DPC250') !== false) {
                        $paperType = 'DPC 250';
                        $color = $colorMapping['K.060.0250'] ?? '#fff2cc';
                    } elseif (strpos($nameUpper, 'IVORY 230 IKDP VR') !== false || strpos($nameUpper, 'IVORY230IKDPVR') !== false) {
                        $paperType = 'IVORY 230 IKDP VR';
                        $color = $colorMapping['K.040.0230.PLN.062'] ?? '#d5e8d4';
                    } elseif (strpos($nameUpper, 'IVORY 230 SPN') !== false || strpos($nameUpper, 'IVORY230SPN') !== false) {
                        $paperType = 'IVORY 230 SPN';
                        $color = $colorMapping['K.040.0230.PLN.065'] ?? '#d5e8d4';
                    } elseif (strpos($nameUpper, 'IVORY 230 IK VR') !== false || strpos($nameUpper, 'IVORY230IKVR') !== false) {
                        $paperType = 'IVORY 230 IK VR';
                        $color = $colorMapping['K.040.0230.PLN.061'] ?? '#ffe6cc';
                    } elseif (strpos($nameUpper, 'IVORY SINAR VANDA 220') !== false || strpos($nameUpper, 'IVORYSINARVANDA220') !== false) {
                        $paperType = 'IVORY SINAR VANDA 220';
                        $color = $colorMapping['K.040.0220.PLN.001'] ?? '#dbeafe';
                    }
                }

                return [
                    'id' => $code,
                    'code' => $code,
                    'name' => $paper->Name,
                    'paperType' => $paperType,
                    'color' => $color,
                    'zgsm' => $paper->ZGSM ?? 0,
                    'zlength' => $paper->ZLength ?? 0,
                    'zwidth' => $paper->ZWidth ?? 0,
                    'text' => $code . ' - ' . $paper->Name // Format untuk Select2
                ];
            });

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Error searching paper: ' . $e->getMessage());
            return response()->json(['error' => 'Error searching paper'], 500);
        }
    }

    /**
     * Get locations from masterlocation table (mysql3)
     */
    public function getLocations(Request $request)
    {
        try {
            $locations = DB::connection('mysql3')
                ->table('masterlocation')
                // ->where('is_active', 1)
                ->orderBy('Code')
                ->select('Code', 'Name')
                ->get();

            return response()->json($locations);
        } catch (\Exception $e) {
            Log::error('Error getting locations: ' . $e->getMessage());
            return response()->json(['error' => 'Error getting locations'], 500);
        }
    }

    /**
     * Get stock kertas untuk location tertentu
     */
    public function getPaperStockByLocation(Request $request)
    {
        // Validasi manual untuk materialcode karena bisa string atau array
        $request->validate([
            'location' => 'required|string',
            'unit_option' => 'nullable|string|in:SKU,Sold,Smallest', // Default: Smallest
        ]);

        try {
            $locationCode = $request->input('location');
            $unitOption = $request->input('unit_option', 'Smallest');

            // Validate unit_option
            $allowedUnits = ['SKU', 'Sold', 'Smallest'];
            if (!in_array($unitOption, $allowedUnits)) {
                $unitOption = 'Smallest';
            }

            // Escape location code
            $locationCodeEscaped = addslashes($locationCode);
            $unitOption2 = $unitOption;

            // Handle materialcode parameter (bisa string atau array dari query parameter)
            // Materialcode bisa dikirim sebagai materialcode[]=... atau materialcode=...
            $materialCodes = $request->input('materialcode');

            // Normalize materialcode ke array
            if ($materialCodes) {
                if (!is_array($materialCodes)) {
                    // Jika string, convert ke array dengan satu element
                    $materialCodes = [$materialCodes];
                }
            } else {
                $materialCodes = null;
            }

            // Handle materialcode filter untuk query
            $materialCodeFilter = '';
            if ($materialCodes && is_array($materialCodes) && count($materialCodes) > 0) {
                // Filter berdasarkan multiple codes
                $escapedCodes = array_map(function($code) {
                    return "'" . addslashes($code) . "'";
                }, array_filter($materialCodes)); // Filter null values
                if (count($escapedCodes) > 0) {
                    $materialCodeFilter = "AND mm.Code IN (" . implode(',', $escapedCodes) . ")";
                }
            }

            // Query untuk mencari stock kertas (Code dimulai dengan 'K.')
            // Hasil dibagi 500 untuk konversi ke RIM (1 RIM = 500 lembar, dari LBR ke RIM)
            $query = "
                SELECT
                    mm.Code,
                    mm.Name,
                    sb.Location,
                    'RIM' AS Unit,
                    IFNULL((SUM(sb.QtyEnd - sb.QtyBook) / COALESCE(NULLIF(muc.Content, 0), 1)) / 500, 0) AS StockQty,
                    IFNULL(SUM(sb.QtyEnd - sb.QtyBook), 0) AS DetailStockQty
                FROM
                    mastermaterial AS mm
                    INNER JOIN masterunitconversion AS muc ON muc.MaterialCode = mm.Code
                    AND muc.Unit = (
                        CASE
                            WHEN '{$unitOption}' = 'SKU' THEN mm.SKUUnit
                            WHEN '{$unitOption2}' = 'Sold' THEN mm.SoldUnit
                            ELSE mm.SmallestUnit
                        END
                    )
                    LEFT JOIN stockbalance AS sb ON sb.MaterialCode = mm.Code
                    AND sb.Periode = DATE_ADD(CURDATE(), INTERVAL DAY(CURDATE()) * -1 + 1 DAY)
                    AND sb.Location = '{$locationCodeEscaped}'
                WHERE
                    mm.Code LIKE 'K.%'
                    {$materialCodeFilter}
                GROUP BY
                    mm.Code, mm.Name, sb.Location, muc.Content, mm.SKUUnit, mm.SoldUnit, mm.SmallestUnit
                ORDER BY
                    mm.Code
            ";

            try {
                $results = DB::connection('mysql3')->select($query);
            } catch (\Exception $queryError) {
                // Fallback tanpa function
                if (strpos($queryError->getMessage(), 'func_splitstockbyunit') !== false ||
                    strpos($queryError->getMessage(), 'FUNCTION') !== false ||
                    strpos($queryError->getMessage(), 'does not exist') !== false) {

                    $queryFallback = "
                        SELECT
                            mm.Code,
                            mm.Name,
                            sb.Location,
                            'RIM' AS Unit,
                            IFNULL((SUM(sb.QtyEnd - sb.QtyBook) / COALESCE(NULLIF(muc.Content, 0), 1)) / 500, 0) AS StockQty,
                            IFNULL(SUM(sb.QtyEnd - sb.QtyBook), 0) AS DetailStockQty
                        FROM
                            mastermaterial AS mm
                            INNER JOIN masterunitconversion AS muc ON muc.MaterialCode = mm.Code
                            AND muc.Unit = (
                                CASE
                                    WHEN '{$unitOption}' = 'SKU' THEN mm.SKUUnit
                                    WHEN '{$unitOption2}' = 'Sold' THEN mm.SoldUnit
                                    ELSE mm.SmallestUnit
                                END
                            )
                            LEFT JOIN stockbalance AS sb ON sb.MaterialCode = mm.Code
                            AND sb.Periode = DATE_ADD(CURDATE(), INTERVAL DAY(CURDATE()) * -1 + 1 DAY)
                            AND sb.Location = '{$locationCodeEscaped}'
                        WHERE
                            mm.Code LIKE 'K.%'
                            {$materialCodeFilter}
                        GROUP BY
                            mm.Code, mm.Name, sb.Location, muc.Content, mm.SKUUnit, mm.SoldUnit, mm.SmallestUnit
                        ORDER BY
                            mm.Code
                    ";

                    $results = DB::connection('mysql3')->select($queryFallback);
                } else {
                    Log::error('Error getting paper stock: ' . $queryError->getMessage());
                    throw $queryError;
                }
            }

            // Format hasil sebagai object dengan Code sebagai key untuk memudahkan lookup
            $stockByCode = [];
            foreach ($results as $row) {
                // Pastikan StockQty adalah float (PHP menggunakan titik sebagai separator desimal secara default)
                $stockQty = (float) $row->StockQty;

                $stockByCode[$row->Code] = [
                    'code' => $row->Code,
                    'name' => $row->Name,
                    'location' => $row->Location,
                    'unit' => $row->Unit,
                    'stockQty' => $stockQty,
                    'detailStockQty' => $row->DetailStockQty
                ];
            }

            return response()->json([
                'success' => true,
                'location' => $locationCode,
                'unit_option' => $unitOption,
                'materialcodes' => $materialCodes,
                'stock' => $stockByCode
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors dengan JSON response
            Log::error('Validation error getting paper stock: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'error' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error getting paper stock by location: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Error getting paper stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display check stock page
     */
    public function checkStock()
    {
        // Get locations for dropdown
        $locations = DB::connection('mysql3')
            ->table('masterlocation')
            ->orderBy('Code')
            ->select('Code', 'Name')
            ->get();

        // Get months for dropdown
        $months = [
            'JAN' => 'Januari',
            'FEB' => 'Februari',
            'MAR' => 'Maret',
            'APR' => 'April',
            'MAY' => 'Mei',
            'JUN' => 'Juni',
            'JUL' => 'Juli',
            'AUG' => 'Agustus',
            'SEP' => 'September',
            'OCT' => 'Oktober',
            'NOV' => 'November',
            'DEC' => 'Desember',
        ];

        return view('main.paper-procurement.check-stock', compact('locations', 'months'));
    }

    /**
     * Execute stock query
     */
    public function executeStockQuery(Request $request)
    {
        $request->validate([
            'period' => 'required|string', // Format: YYYY-MM
            'location' => 'required|string', // Location Code
            'unit_option' => 'nullable|string|in:SKU,Sold,Smallest' // Default: Smallest
        ]);

        try {
            $period = $request->input('period'); // Format: "2025-01"
            $locationCode = $request->input('location');
            $unitOption = $request->input('unit_option', 'Smallest'); // Default Smallest

            // Parse period menjadi tanggal pertama bulan
            $periodDate = $this->parsePeriodToDate($period);

            // Validate unit_option untuk keamanan
            $allowedUnits = ['SKU', 'Sold', 'Smallest'];
            if (!in_array($unitOption, $allowedUnits)) {
                $unitOption = 'Smallest';
            }

            // Escape location code untuk keamanan (prevent SQL injection)
            $locationCodeEscaped = addslashes($locationCode);

            // Untuk unit option, gunakan variabel terpisah seperti di InventoryController
            $unitOption2 = $unitOption; // Sama seperti di InventoryController

            // Build query dengan hasil dibagi 500 untuk konversi ke RIM (1 RIM = 500 lembar, dari LBR ke RIM)
            $query = "
                SELECT
                    mm.Code,
                    mm.Name,
                    sb.Location,
                    'RIM' AS Unit,
                    IFNULL((SUM(sb.QtyEnd - sb.QtyBook) / COALESCE(NULLIF(muc.Content, 0), 1)) / 500, 0) AS StockQty,
                    func_splitstockbyunit(mm.Code, IFNULL(SUM(sb.QtyEnd - sb.QtyBook), 0)) AS DetailStockQty
                FROM
                    mastermaterial AS mm
                    INNER JOIN masterunitconversion AS muc ON muc.MaterialCode = mm.Code
                    AND muc.Unit = (
                        CASE
                            WHEN '{$unitOption}' = 'SKU' THEN mm.SKUUnit
                            WHEN '{$unitOption2}' = 'Sold' THEN mm.SoldUnit
                            ELSE mm.SmallestUnit
                        END
                    )
                    LEFT JOIN stockbalance AS sb ON sb.MaterialCode = mm.Code
                    AND sb.Periode = '{$periodDate}'
                    AND sb.Location = '{$locationCodeEscaped}'
                WHERE
                    TRUE
                GROUP BY
                    mm.Code, mm.Name, sb.Location, muc.Content, mm.SKUUnit, mm.SoldUnit, mm.SmallestUnit
                ORDER BY
                    mm.Code
            ";

            // Log query untuk debugging
            Log::info('Executing stock query', [
                'query' => $query,
                'params' => [
                    'period' => $period,
                    'periodDate' => $periodDate,
                    'location' => $locationCode,
                    'unitOption' => $unitOption
                ]
            ]);

            try {
                $results = DB::connection('mysql3')->select($query);
            } catch (\Exception $queryError) {
                // Jika error karena function tidak ada, coba query tanpa function
                if (strpos($queryError->getMessage(), 'func_splitstockbyunit') !== false ||
                    strpos($queryError->getMessage(), 'FUNCTION') !== false ||
                    strpos($queryError->getMessage(), 'does not exist') !== false) {
                    Log::warning('Function func_splitstockbyunit not found, using fallback query');

                    // Query tanpa function - DetailStockQty akan berisi StockQty
                    // Hasil dikali 500 untuk konversi ke RIM (1 RIM = 500 lembar)
                    $queryFallback = "
                        SELECT
                            mm.Code,
                            mm.Name,
                            sb.Location,
                            'RIM' AS Unit,
                            IFNULL((SUM(sb.QtyEnd - sb.QtyBook) / COALESCE(NULLIF(muc.Content, 0), 1)) / 500, 0) AS StockQty,
                            IFNULL(SUM(sb.QtyEnd - sb.QtyBook), 0) AS DetailStockQty
                        FROM
                            mastermaterial AS mm
                            INNER JOIN masterunitconversion AS muc ON muc.MaterialCode = mm.Code
                            AND muc.Unit = (
                                CASE
                                    WHEN '{$unitOption}' = 'SKU' THEN mm.SKUUnit
                                    WHEN '{$unitOption2}' = 'Sold' THEN mm.SoldUnit
                                    ELSE mm.SmallestUnit
                                END
                            )
                            LEFT JOIN stockbalance AS sb ON sb.MaterialCode = mm.Code
                            AND sb.Periode = '{$periodDate}'
                            AND sb.Location = '{$locationCodeEscaped}'
                        WHERE
                            TRUE
                        GROUP BY
                            mm.Code, mm.Name, sb.Location, muc.Content, mm.SKUUnit, mm.SoldUnit, mm.SmallestUnit
                        ORDER BY
                            mm.Code
                    ";

                    try {
                        $results = DB::connection('mysql3')->select($queryFallback);
                        $query = $queryFallback; // Update query yang digunakan untuk response
                    } catch (\Exception $fallbackError) {
                        Log::error('Fallback query also failed: ' . $fallbackError->getMessage());
                        throw $fallbackError;
                    }
                } else {
                    Log::error('Query execution failed: ' . $queryError->getMessage());
                    Log::error('Failed query: ' . $query);
                    throw $queryError;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => count($results),
                'period' => $period,
                'period_date' => $periodDate,
                'location' => $locationCode,
                'unit_option' => $unitOption,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            Log::error('Error executing stock query: ' . $e->getMessage());
            Log::error('Query params: ' . json_encode($request->all()));
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => 'Error executing query: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Parse period string to date (first day of month)
     * Format stockbalance.Periode biasanya DATE format (YYYY-MM-DD) atau DATE_ADD format
     */
    private function parsePeriodToDate($period)
    {
        // Format: "2025-01" -> "2025-01-01"
        if (preg_match('/^(\d{4})-(\d{2})$/', $period, $matches)) {
            return $matches[1] . '-' . $matches[2] . '-01';
        }

        // Default: current month first day
        return date('Y-m-01');
    }

    /**
     * Get PO remain by MaterialCode
     * Returns PO data with QtyRemain = Qty - QtyReceived
     */
    public function getPORemain(Request $request)
    {
        // dd($request->all());
        try {
            $materialCodes = $request->input('material_codes', []);

            if (empty($materialCodes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material codes are required'
                ], 400);
            }

            $poService = new POService();
            $poRemain = $poService->getPORemainByMaterialCode($materialCodes);

            // dd($poRemain);

            // Group by MaterialCode for easier frontend processing
            $grouped = $poRemain->groupBy('MaterialCode')->map(function ($items) {
                //
                return $items->map(function ($item) {
                    return [
                        'DocNo' => $item->DocNo,
                        'MaterialCode' => $item->MaterialCode,
                        'MaterialName' => $item->MaterialName,
                        'Qty' => $item->Qty,
                        'QtyReceived' => $item->QtyReceived ?? 0,
                        'QtyRemain' => $item->QtyRemain,
                        'Unit' => $item->Unit,
                        'DocDate' => $item->DocDate,
                        // 'SupplierCode' => $item->SupplierCode,
                        // 'SupplierName' => $item->SupplierName,
                    ];
                })->values();
            });

            return response()->json([
                'success' => true,
                'data' => $grouped,
                'raw' => $poRemain->values()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting PO remain: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching PO remain: ' . $e->getMessage()
            ], 500);
        }
    }
}

