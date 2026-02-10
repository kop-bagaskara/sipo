<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function Termwind\parse;

class InventoryController extends Controller
{
    public function search(Request $request)
    {
        // dd($request->all());

        try {
            $kodeDesign = $request->kode_design;

            // Query untuk mencari data di masterbomh
            $mainItems = DB::connection('mysql3')->table('masterbomh')
                ->where('MaterialCode', 'like', '%' . $kodeDesign . '%')
                ->select('Formula', 'MaterialCode', 'Qty', 'Unit')
                ->orderBy('Formula', 'DESC') // Hanya pilih Formula dari masterbomh
                ->get();

            // Ambil detail dari masterbomd untuk setiap Formula
            $data = $mainItems->map(function ($item) {
                $item->details = DB::connection('mysql3')->table('masterbomd')
                    ->leftJoin('mastermaterial', 'mastermaterial.Code', 'masterbomd.MaterialCode')
                    ->where('Formula', $item->Formula)
                    ->select('masterbomd.MaterialCode', 'mastermaterial.Name', 'masterbomd.Unit', 'masterbomd.Qty') // Pilih kolom detail yang diperlukan
                    ->get();
                return $item;
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function SalesOrderController(Request $request)
    {
        try {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $salesOrders = DB::connection('mysql3')->table('salesorderh')
                ->join('salesorderd', 'salesorderh.DocNo', '=', 'salesorderd.DocNo')
                ->whereBetween('salesorderh.DocDate', [$startDate, $endDate])
                ->select(
                    'salesorderh.DocNo as doc_no',
                    'salesorderh.DocDate as delivery_date',
                    'salesorderd.MaterialCode as item',
                    'salesorderd.Qty as quantity',
                    'salesorderd.Unit as unit',
                    'salesorderd.QtyDelivered as qty_delivered',
                    DB::raw('(salesorderd.Qty - salesorderd.QtyDelivered) as qty_open')
                )
                ->orderBy('salesorderh.DocDate', 'ASC')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $salesOrders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function calculateMaterial(Request $request)
    {
        try {
            $selectedSalesOrders = $request->sales_orders;
            $aggregatedMaterials = [];

            foreach ($selectedSalesOrders as $so) {
                // dd($so);
                $docNo = $so['doc_no'];
                $deliveryDate = $so['del_date'];
                $finishedProductMaterialCode = $so['item'];
                $qtyOpen = (float) $so['qty_open'];
                $unitSalesOrder = $so['unit'];

                // dd($finishedProductMaterialCode);

                // Fetch BOM for the finished product from masterbomh
                $bomHeaders = DB::connection('mysql3')->table('masterbomh')
                    ->where('Formula', 'like', '%' . $finishedProductMaterialCode . '%') // Use LIKE for partial matching
                    ->get(); // Change from ->first() to ->get()

                // dd($bomHeaders);

                foreach ($bomHeaders as $bomHeader) {
                    // dd($bomHeader);
                    $bomHeaderQty = (float) $bomHeader->Qty; // Get Qty from masterbomh
                    $bomHeaderSatuan = $bomHeader->Unit; // Get Unit from masterbomh

                    // dd($unitSalesOrder, $bomHeaderSatuan);

                    // --- Konversi langsung PCS ke LBR tanpa cek tabel konversi ---
                    $qtyOpenForCalc = $qtyOpen;
                    if (strtoupper($unitSalesOrder) !== strtoupper($bomHeaderSatuan)) {
                        if (strtoupper($unitSalesOrder) == 'PCS' && strtoupper($bomHeaderSatuan) == 'LBR') {

                            $workorder = DB::connection('mysql3')->table('workorderh')
                                ->where('sodocno', $docNo)
                                ->where('status', '!=', 'DELETED')
                                ->first();


                            $qtyUp = $workorder->Zupcetak;

                            $qtyOpenForCalc = $qtyOpen / intval($qtyUp);
                        }
                    }

                    // dd($qtyOpenForCalc);
                    // ------------------------------------------------------

                    // dd($bomHeader->Formula);

                    // Use the Formula from masterbomh to get details from masterbomd
                    $bomDetails = DB::connection('mysql3')->table('masterbomd')
                        ->leftJoin('mastermaterial', 'mastermaterial.Code', '=', 'masterbomd.MaterialCode')
                        ->where('masterbomd.Formula', $bomHeader->Formula)
                        ->select('masterbomd.MaterialCode', 'masterbomd.Qty', 'masterbomd.Unit', 'mastermaterial.Name')
                        ->get();
                    // dd($bomDetails);

                    foreach ($bomDetails as $detail) {
                        // dd($detail);
                        $materialCode = $detail->MaterialCode;
                        $materialName = $detail->Name;
                        $unit = $detail->Unit;
                        // Kalkulasi kebutuhan material pakai qtyOpenForCalc (sudah dikonversi jika perlu)
                        // dd($bomHeaderQty, $qtyOpenForCalc);
                        // dd($detail->Qty);
                        $neededQtyForThisSO = ($bomHeaderQty > 0) ? (float) $detail->Qty / $bomHeaderQty * $qtyOpenForCalc : 0;

                        if (array_key_exists($materialCode, $aggregatedMaterials)) {
                            // Add doc_no and needed_qty_for_so to SourceItems if not already present, or update qty
                            $found = false;
                            // Check if neededQtyForThisSO is greater than 0 before adding to SourceItems
                            if ($neededQtyForThisSO > 0) {
                                foreach ((array) $aggregatedMaterials[$materialCode]['SourceItems'] as &$sourceItem) {
                                    if ($sourceItem['doc_no'] === $docNo) {
                                        $sourceItem['needed_qty_for_so'] += $neededQtyForThisSO;
                                        $found = true;
                                        break;
                                    }
                                }
                                if (!$found) {
                                    $aggregatedMaterials[$materialCode]['SourceItems'][] = [
                                        'doc_no' => $docNo,
                                        'so_item' => $finishedProductMaterialCode, // Add finished product item from SO
                                        'needed_item_code' => $materialCode . ' - ' . $materialName, // Ensure name is always included
                                        'needed_qty_for_so' => $neededQtyForThisSO,
                                        'delivery_date' => $deliveryDate, // Standardize to 'delivery_date'
                                    ];
                                }
                            }
                        } else {
                            // Initialize only if neededQtyForThisSO is greater than 0
                            if ($neededQtyForThisSO > 0) {
                                $aggregatedMaterials[$materialCode] = [
                                    'MaterialCode' => $materialCode,
                                    'Name' => $materialName,
                                    'Unit' => $unit,
                                    'SourceItems' => [[
                                        'doc_no' => $docNo,
                                        'so_item' => $finishedProductMaterialCode,
                                        'needed_item_code' => $materialCode . ' - ' . $materialName,
                                        'needed_qty_for_so' => $neededQtyForThisSO,
                                        'delivery_date' => $deliveryDate // Standardize to 'delivery_date'
                                    ]]
                                ];
                            }
                        }
                    }
                }
            }

            // dd($aggregatedMaterials);

            // After processing all sales orders and BOMs, calculate the total NeededQuantity for each aggregated material
            foreach ($aggregatedMaterials as $materialCode => &$materialData) {
                $totalNeeded = 0;
                $earliestDeliveryDate = null;

                foreach ($materialData['SourceItems'] as $sourceItem) {
                    $totalNeeded += $sourceItem['needed_qty_for_so'];

                    // Determine the earliest delivery date for this material
                    if (isset($sourceItem['delivery_date']) && $sourceItem['delivery_date'] !== null) {
                        if ($earliestDeliveryDate === null || $sourceItem['delivery_date'] < $earliestDeliveryDate) {
                            $earliestDeliveryDate = $sourceItem['delivery_date'];
                        }
                    }
                }
                $materialData['NeededQuantity'] = $totalNeeded;
                $materialData['EarliestDeliveryDate'] = $earliestDeliveryDate; // Add earliest delivery date

                // Fetch current stock balance for this material using mysql3 connection
                $unitOption = 'SKU'; // Bisa diganti dinamis jika perlu
                $unitOption2 = 'Sold'; // Bisa diganti dinamis jika perlu
                $location = 'G19BK'; // Bisa diganti dinamis jika perlu
                $currentMaterialCode = $materialData['MaterialCode']; // Get the actual material code
                // dd($currentMaterialCode);

                $query = "
                    SELECT
                        mm.Code,
                        mm.Name,
                        sb.Location,
                        (
                            CASE
                                WHEN '$unitOption' = 'SKU' THEN mm.SKUUnit
                                WHEN '$unitOption2' = 'Sold' THEN mm.SoldUnit
                                ELSE mm.SmallestUnit
                            END
                        ) AS Unit,
                        IFNULL(SUM(sb.QtyEnd - sb.QtyBook) / COALESCE(NULLIF(muc.Content, 0), 1), 0) AS StockQty,
                        func_splitstockbyunit(mm.Code, IFNULL(SUM(sb.QtyEnd - sb.QtyBook), 0)) AS DetailStockQty
                    FROM
                        mastermaterial AS mm
                        INNER JOIN masterunitconversion AS muc ON muc.MaterialCode = mm.Code
                        AND muc.Unit = (
                            CASE
                                WHEN '$unitOption' = 'SKU' THEN mm.SKUUnit
                                WHEN '$unitOption2' = 'Sold' THEN mm.SoldUnit
                                ELSE mm.SmallestUnit
                            END
                        )
                        LEFT JOIN stockbalance AS sb ON sb.MaterialCode = mm.Code
                        AND sb.Periode = DATE_ADD(CURDATE(), INTERVAL DAY(CURDATE()) * -1 + 1 DAY)
                        AND sb.Location = '$location'
                    WHERE
                        mm.Code = ?
                    GROUP BY
                        mm.Code, mm.Name, sb.Location, muc.Content, mm.SKUUnit, mm.SoldUnit, mm.SmallestUnit
                    ";

                // dd($query);
                $stockResult = DB::connection('mysql3')->select($query, [$currentMaterialCode]);

                // dd($stockResult);

                $currentStock = !empty($stockResult) ? (float) $stockResult[0]->StockQty : 0;
                $stockG19PR = 0; // Initialize stockG19PR

                // Stock G19BK
                $locationBK = 'G19BK';
                $queryBK = str_replace($location, $locationBK, $query); // gunakan query yang sudah ada, ganti lokasi
                $stockResultBK = DB::connection('mysql3')->select($queryBK, [$currentMaterialCode]);
                $stockG19PR = !empty($stockResultBK) ? (float) $stockResultBK[0]->StockQty : 0;

                // Stock G19PR
                $locationPR = 'G19PR';
                $queryPR = str_replace($locationBK, $locationPR, $queryBK);
                $stockResultPR = DB::connection('mysql3')->select($queryPR, [$currentMaterialCode]);
                $stockG19PR = !empty($stockResultPR) ? (float) $stockResultPR[0]->StockQty : 0;

                $materialData['CurrentStock'] = $currentStock;
                $materialData['StockG19PR'] = $stockG19PR;
                $materialData['StockSim'] = $materialData['CurrentStock'] - $materialData['NeededQuantity']; // Calculate Stock Sim
                //  dd($materialData);
                // Add CurrentStock to each SourceItem for frontend calculation
                // foreach ($materialData['SourceItems'] as &$sourceItem) {
                //     $sourceItem['current_stock_at_so_level'] = $materialData['CurrentStock'];
                // }
            }

            // dd($aggregatedMaterials);

            // Convert associative array to indexed array for DataTables
            $finalResult = array_values($aggregatedMaterials);

            return response()->json([
                'success' => true,
                'data' => $finalResult
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function exportMaterialNeeds(Request $request)
    {
        $data = $request->input('data');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Header
        $sheet->fromArray([
            ['#', 'Material Code', 'Material Name', 'Needed Quantity', 'Qty Current Stock', 'Stock Sim', 'Unit', 'Sales Order Doc No', 'Delivery Date', 'SO Item', 'Needed Item Code', 'Needed Quantity for (SO)', 'Cumulative Stock Sim']
        ], null, 'A1');
        $row = 2;
        foreach ($data as $idx => $material) {
            $sheet->fromArray([
                $idx + 1,
                $material['MaterialCode'],
                $material['Name'],
                $material['NeededQuantity'],
                $material['CurrentStock'],
                $material['StockSim'],
                $material['Unit'],
                '',
                '',
                '',
                '',
                '',
                ''
            ], null, 'A' . $row);
            $row++;
            if (isset($material['SourceItems'])) {
                foreach ($material['SourceItems'] as $detail) {
                    $sheet->fromArray([
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $detail['doc_no'],
                        $detail['delivery_date'],
                        $detail['so_item'],
                        $detail['needed_item_code'],
                        $detail['needed_qty_for_so'],
                        ''
                    ], null, 'A' . $row);
                    $row++;
                }
            }
        }
        $writer = new Xlsx($spreadsheet);
        $filename = 'material_needs_export.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);
        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
