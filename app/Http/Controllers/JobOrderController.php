<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class JobOrderController extends Controller
{
        /**
     * Check job order status for work orders in plan
     */
    public function checkJobOrderStatus(Request $request): JsonResponse
    {
        try {
            $planId = $request->input('plan_id');

            if (!$planId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan ID is required'
                ], 400);
            }

            // Get all plan items for the specified plan from plan_first_productions table
            $planItems = DB::table('tb_plan_first_productions')
                ->where('code_plan', $planId)
                ->select('id', 'code_item', 'code_machine', 'start_jam', 'end_jam', 'quantity', 'process', 'wo_docno')
                ->get();

            $jobOrderStatus = [];

            foreach ($planItems as $item) {

                $materialCode = $item->code_item .'.WIP.'. $item->process;
                // dd($materialCode);
                // Check if job order exists for this item in the existing job_order table
                $jobOrder = DB::connection('mysql3')
                    ->table('joborder')
                    ->where('MaterialCode', 'like', '%' . $materialCode .'%')
                    ->where('IODocNo', 'like', '%' . $item->wo_docno .'%')
                    ->where('status', '!=', 'DELETED')
                    ->first();
                // dd($jobOrder);

                $jobOrderStatus[] = [
                    'plan_item_id' => $item->id,
                    'code_item' => $item->code_item,
                    'code_machine' => $item->code_machine,
                    'has_job_order' => $jobOrder ? true : false,
                    'job_order_number' => $jobOrder ? $jobOrder->DocNo : null,
                    'job_order_status' => $jobOrder ? $jobOrder->Status : null,
                    'needs_attention' => !$jobOrder // true if no job order exists
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $jobOrderStatus,
                'message' => 'Job order status checked successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking job order status: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
     * Get job order details for a specific item
     */
    public function getJobOrderDetails(Request $request): JsonResponse
    {
        try {
            $codeItem = $request->input('code_item');

            if (!$codeItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item code is required'
                ], 400);
            }

            $jobOrder = DB::table('job_order')
                ->where('code_item', $codeItem)
                ->first();

            if (!$jobOrder) {
                return response()->json([
                    'success' => false,
                    'message' => 'No job order found for this item'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $jobOrder
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting job order details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check job order status for multiple items by their codes
     */
    public function checkJobOrderStatusByItems(Request $request): JsonResponse
    {
        try {
            $itemCodes = $request->input('item_codes');

            if (!$itemCodes || !is_array($itemCodes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item codes array is required'
                ], 400);
            }

            $jobOrderStatus = [];

            foreach ($itemCodes as $codeItem) {
                // Check if job order exists for this item
                $jobOrder = DB::table('job_order')
                    ->where('code_item', $codeItem)
                    ->where('status', '!=', 'cancelled')
                    ->first();

                $jobOrderStatus[] = [
                    'code_item' => $codeItem,
                    'has_job_order' => $jobOrder ? true : false,
                    'job_order_number' => $jobOrder ? $jobOrder->job_order_number : null,
                    'job_order_status' => $jobOrder ? $jobOrder->status : null,
                    'needs_attention' => !$jobOrder // true if no job order exists
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $jobOrderStatus,
                'message' => 'Job order status checked successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking job order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary of missing job orders for a plan
     */
    public function getMissingJobOrdersSummary(Request $request): JsonResponse
    {
        try {
            $planId = $request->input('plan_id');

            if (!$planId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plan ID is required'
                ], 400);
            }

                        // Count items without job orders
            $totalItems = DB::table('plan_first_productions')
                ->where('code_plan', $planId)
                ->count();

            $itemsWithJobOrders = DB::table('plan_first_productions as pp')
                ->join('job_order as jo', 'pp.code_item', '=', 'jo.code_item')
                ->where('pp.code_plan', $planId)
                ->where('jo.status', '!=', 'cancelled')
                ->count();

            $missingJobOrders = $totalItems - $itemsWithJobOrders;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_items' => $totalItems,
                    'items_with_job_orders' => $itemsWithJobOrders,
                    'missing_job_orders' => $missingJobOrders,
                    'completion_percentage' => $totalItems > 0 ? round(($itemsWithJobOrders / $totalItems) * 100, 2) : 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job order by WO DOCNO from mysql3
     * First search: WHERE WODocNo = wo_docno
     * If not found, fallback: WHERE IODocNo = wo_docno
     * Both with: MaterialCode LIKE '%WIP.CTK%' AND status != 'DELETED'
     * Returns first result
     */
    public function getJobOrderByWODocNo(Request $request): JsonResponse
    {
        try {
            $woDocNo = $request->input('wo_docno');

            if (!$woDocNo) {
                return response()->json([
                    'success' => false,
                    'message' => 'WO DOCNO is required'
                ], 400);
            }

            // Query job order from mysql3 - pertama cari dengan WODocNo
            $jobOrder = DB::connection('mysql3')
                ->table('joborder')
                ->where('IODocNo', $woDocNo)
                ->where('MaterialCode', 'LIKE', '%WIP.CTK%')
                ->where('status', '!=', 'DELETED')
                ->orderBy('DocNo', 'DESC')
                ->first();

            // Jika tidak ketemu dengan WODocNo, cari dengan IODocNo
            if (!$jobOrder) {
                $jobOrder = DB::connection('mysql3')
                    ->table('joborder')
                    ->where('WODocNo', $woDocNo)
                    ->where('MaterialCode', 'LIKE', '%WIP.CTK%')
                    ->where('status', '!=', 'DELETED')
                    ->orderBy('DocNo', 'DESC')
                    ->first();
            }

            if (!$jobOrder) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No job order found for this WO DOCNO'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'doc_no' => $jobOrder->DocNo ?? null,
                    'wo_docno' => $jobOrder->WODocNo ?? null,
                    'material_code' => $jobOrder->MaterialCode ?? null,
                    'status' => $jobOrder->Status ?? null,
                    'qty' => $jobOrder->Qty ?? null,
                ],
                'message' => 'Job order found successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting job order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job orders for multiple WO DOCNOs
     */
    public function getJobOrdersByWODocNos(Request $request): JsonResponse
    {
        try {
            $woDocNos = $request->input('wo_docnos');

            if (!$woDocNos || !is_array($woDocNos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'WO DOCNOs array is required'
                ], 400);
            }

            $jobOrders = [];

            foreach ($woDocNos as $woDocNo) {
                if (empty($woDocNo)) {
                    continue;
                }

                // Query job order from mysql3 - pertama cari dengan WODocNo
                $jobOrder = DB::connection('mysql3')
                    ->table('joborder')
                    ->where('IODocNo', $woDocNo)
                    ->where('MaterialCode', 'LIKE', '%WIP.CTK%')
                    ->where('status', '!=', 'DELETED')
                    ->orderBy('DocNo', 'DESC')
                    ->first();

                // Jika tidak ketemu dengan WODocNo, cari dengan IODocNo
                if (!$jobOrder) {
                    $jobOrder = DB::connection('mysql3')
                        ->table('joborder')
                        ->where('WODocNo', $woDocNo)
                        ->where('MaterialCode', 'LIKE', '%WIP.CTK%')
                        ->where('status', '!=', 'DELETED')
                        ->orderBy('DocNo', 'DESC')
                        ->first();
                }

                $jobOrders[$woDocNo] = $jobOrder ? [
                    'doc_no' => $jobOrder->DocNo ?? null,
                    'wo_docno' => $jobOrder->WODocNo ?? null,
                    'io_docno' => $jobOrder->IODocNo ?? null,
                    'material_code' => $jobOrder->MaterialCode ?? null,
                    'status' => $jobOrder->Status ?? null,
                    'qty' => $jobOrder->Qty ?? null,
                ] : null;
            }

            return response()->json([
                'success' => true,
                'data' => $jobOrders,
                'message' => 'Job orders retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting job orders: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paper size information from masterworkordetemplateh
     * Parse Information field to extract BAHAN1 and UC1 (ukuran cetak)
     */
    public function getPaperSizeByMaterialCode(Request $request): JsonResponse
    {
        try {
            $materialCode = $request->input('material_code');

            if (!$materialCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material code is required'
                ], 400);
            }

            // Query dari mysql3.masterworkordetemplateh
            $template = DB::connection('mysql3')
                ->table('masterworkordertemplateh')
                ->where('MaterialCode', 'like', '%' . $materialCode .'%')
                ->first();

            if (!$template || !isset($template->Information)) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No template found for this material code'
                ]);
            }

            $information = $template->Information;

            // Parse Information field
            $bahan1 = null;
            $ukuranCetak = null;

            // Extract BAHAN1: (ambil 12 karakter setelah "BAHAN1: ")
            if (preg_match('/BAHAN1:\s*(.{0,12})/i', $information, $matches)) {
                $bahan1 = trim($matches[1]);
            }

            // Extract UC1: (ambil 10 karakter setelah "UC1: ")
            if (preg_match('/UC1:\s*(.{0,12})/i', $information, $matches)) {
                $ukuranCetak = trim($matches[1]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'material_code' => $materialCode,
                    'bahan1' => $bahan1,
                    'ukuran_cetak' => $ukuranCetak,
                    'information' => $information // Full information for debugging
                ],
                'message' => 'Paper size information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting paper size: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paper size for multiple material codes
     */
    public function getPaperSizesByMaterialCodes(Request $request): JsonResponse
    {
        try {
            $materialCodes = $request->input('material_codes');

            if (!$materialCodes || !is_array($materialCodes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material codes array is required'
                ], 400);
            }

            $results = [];

            foreach ($materialCodes as $materialCode) {
                // Query dari mysql3.masterworkordetemplateh
                $template = DB::connection('mysql3')
                    ->table('masterworkordertemplateh')
                    ->where('MaterialCode', $materialCode)
                    ->first();

                if ($template && isset($template->Information)) {
                    $information = $template->Information;

                    // Parse Information field
                    $bahan1 = null;
                    $ukuranCetak = null;

                    // Extract BAHAN1: (ambil 12 karakter setelah "BAHAN1: ")
                    if (preg_match('/BAHAN1:\s*(.{0,12})/i', $information, $matches)) {
                        $bahan1 = trim($matches[1]);
                    }

                    // Extract UC1: (ambil 10 karakter setelah "UC1: ")
                    if (preg_match('/UC1:\s*(.{0,12})/i', $information, $matches)) {
                        $ukuranCetak = trim($matches[1]);
                    }

                    $results[$materialCode] = [
                        'bahan1' => $bahan1,
                        'ukuran_cetak' => $ukuranCetak,
                        'information' => $information
                    ];
                } else {
                    $results[$materialCode] = null;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Paper size information retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting paper sizes: ' . $e->getMessage()
            ], 500);
        }
    }
}
