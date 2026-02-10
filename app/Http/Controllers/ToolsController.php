<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrderExport;
use App\Models\ToolsReportDataPurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ToolsController extends Controller
{
    public function indexToolsStocTransfer()
    {
        return view('main.tools.page-stc');
    }
    private function cleanExcelString($string)
    {
        // Hapus karakter tak terlihat (spasi tersembunyi, tab, dsb.)
        $string = preg_replace('/^[\p{Z}\p{C}]+|[\p{Z}\p{C}]+$/u', '', $string);

        // Pastikan tetap dalam format string yang bersih
        return trim($string);
    }

    private function fixWOTFormat($wot)
    {
        if (preg_match('/^WOT-(\d{1,2})(\d{2})(\d{2})-(\d{4})$/', $wot, $matches)) {
            return sprintf("WOT-%02d%s%s-%s", $matches[1], $matches[2], $matches[3], $matches[4]);
        }
        return $wot; // Kalau gak cocok, biarin apa adanya
    }

    private function cleanWOT($wot)
    {
        $wot = trim($wot);
        return $wot;
    }

    private function findJO($wot, $materialCode)
    {
        // dd($materialCode);
        $wot = $this->cleanWOT($wot);
        // dd($wot);
        $query = DB::connection('mysql3')
            ->table('joborder')
            ->where(function ($query) use ($wot) {
                $query->where('IODocNo', $wot)->orWhere('WODocNo', $wot);
            })
            ->where('status', 'FINISH');

        if (str_starts_with($materialCode, 'K.')) {
            $query->where('MaterialCode', 'LIKE', '%PTG%');
        } elseif (str_starts_with($materialCode, 'BP.')) {
            $query->where('MaterialCode', 'LIKE', '%UV%');
        } elseif (str_starts_with($materialCode, 'T.')) {
            $query->where('MaterialCode', 'LIKE', '%CTK%');
        } elseif (str_starts_with($materialCode, 'D.')) {
            $query->where('Location', 'G23FG');
        } elseif (str_starts_with($materialCode, 'F.')) {
            $query->where('MaterialCode', 'LIKE', '%HP%');
        }

        $joData = $query->orderBy('CreatedDate', 'desc')->first();

        return $joData ? $joData->DocNo : '';
    }

    public function filterAndStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Tambahkan header baru
        $headers = array_merge($data[0], ['Date STC', 'Date JOD', 'Conv STC', 'Conv JOD', 'Day']);
        $filteredData = [$headers];

        $newSpreadsheet = new Spreadsheet();
        $newSheet = $newSpreadsheet->getActiveSheet();
        $newSheet->fromArray($headers, null, 'A1');

        $rowIndex = 2; // Mulai dari baris kedua (lewati header)
        foreach ($data as $index => $row) {
            if ($index === 0) continue; // Lewati header

            if (isset($row[1]) && strtoupper(trim($row[1])) === "STC") {
                $colIndex = 1;
                foreach ($row as $value) {
                    if ($colIndex == 7) {
                        $value = $this->cleanExcelString(trim($value));
                        if (str_starts_with($value, 'WOT-')) {
                            $value = $this->fixWOTFormat($value);
                            $value = substr($value, 0, 15);

                            $materialCode = isset($row[16]) ? trim($row[16]) : '';
                            $valueasli = isset($row[6]) ? trim($row[6]) : '';

                            // Temukan JO berdasarkan WOT dan Material Code
                            $joInfo = $this->findJO($value, $materialCode);
                            // dd($value);
                            // $value = $this->findJO($value) . ' >> ' . $value;
                            $value = $joInfo . ' >> ' . $valueasli;
                            // dd($value);
                        }
                        $newSheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($colIndex) . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    } else {
                        $newSheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($colIndex) . $rowIndex, $value ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    }
                    $colIndex++;
                }

                // Kolom tambahan (Date STC, Date JOD, Conv STC, Conv JOD, Day)
                $colZ = Coordinate::stringFromColumnIndex($colIndex);
                $colAA = Coordinate::stringFromColumnIndex($colIndex + 1);
                $colAB = Coordinate::stringFromColumnIndex($colIndex + 2);
                $colAC = Coordinate::stringFromColumnIndex($colIndex + 3);
                $colAD = Coordinate::stringFromColumnIndex($colIndex + 4);

                $newSheet->setCellValue($colZ . $rowIndex, "=MID(A$rowIndex,5,6)");
                $newSheet->setCellValue($colAA . $rowIndex, "=MID(G$rowIndex,5,6)");
                $newSheet->setCellValue($colAB . $rowIndex, "=C$rowIndex");
                $newSheet->setCellValue($colAC . $rowIndex, "=RIGHT($colAA$rowIndex,2)&\"/\"&MID($colAA$rowIndex,3,2)&\"/\"&LEFT($colAA$rowIndex,2)");
                $newSheet->setCellValue($colAD . $rowIndex, "=$colAB$rowIndex-$colAC$rowIndex");

                // dd($row[6]);
                // 🎨 Kasih warna merah kalau JO gak ditemukan
                if (!str_contains($row[6], 'JOD-')) {
                    $newSheet->getStyle("G$rowIndex")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFF0000'); // Warna merah
                }

                $rowIndex++;
            }
        }
        $lastRow = $newSheet->getHighestRow();
        $formulaRow = $lastRow + 2; // Baris awal formula
        $targetColumn = 'AD'; // Kolom yang dihitung
        $categoryColumn = 'AA'; // Kolom kategori

        $totalRow = $formulaRow + 3;
        // **Tambahkan Indikator di Kolom Kategori**
        $newSheet->setCellValue("A$formulaRow", "<0");
        $newSheet->setCellValue("A" . ($formulaRow + 1), "0");
        $newSheet->setCellValue("A" . ($formulaRow + 2), ">0");
        $newSheet->setCellValue("A$totalRow", "Total");

        // **Hitung masing-masing kategori**
        $newSheet->setCellValue("B$formulaRow", "=COUNTIF(\${$targetColumn}\$2:\${$targetColumn}\$$lastRow, \"<0\")");
        $newSheet->setCellValue("B" . ($formulaRow + 1), "=COUNTIF(\${$targetColumn}\$2:\${$targetColumn}\$$lastRow, \"=0\")");
        $newSheet->setCellValue("B" . ($formulaRow + 2), "=COUNTIF(\${$targetColumn}\$2:\${$targetColumn}\$$lastRow, \">0\")");

        // **Hitung Total**
        $newSheet->setCellValue("B$totalRow", "=SUM(B$formulaRow:B" . ($formulaRow + 2) . ")");

        // **Cegah #DIV/0 dengan IF**
        $newSheet->setCellValue("C$formulaRow", "=IF(B$totalRow=0, 0, B$formulaRow/B$totalRow)");
        $newSheet->setCellValue("C" . ($formulaRow + 1), "=IF(B$totalRow=0, 0, B" . ($formulaRow + 1) . "/B$totalRow)");
        $newSheet->setCellValue("C" . ($formulaRow + 2), "=IF(B$totalRow=0, 0, B" . ($formulaRow + 2) . "/B$totalRow)");

        // **Format sebagai persen**
        $percentageStyle = $newSheet->getStyle("C$formulaRow:C" . ($formulaRow + 2));
        $percentageStyle->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);


        // Simpan file hasil filter ke storage
        $fileName = 'filtered_stock_transfer.xlsx';
        $filePath = storage_path('app/public/' . $fileName);
        $writer = new Xlsx($newSpreadsheet);
        $writer->save($filePath);

        return back()->with('success', 'File berhasil difilter!')->with('downloadLink', asset('storage/' . $fileName));
    }

    public function downloadFilteredFile()
    {
        $fileName = 'filtered_stock_transfer.xlsx';
        $filePath = storage_path('app/public/' . $fileName);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan!');
        }

        return response()->download($filePath);
    }



    // PURCHASE ORDER
    public function indexToolsPurchaseOrderReport()
    {
        $kodeitem = ToolsReportDataPurchaseOrder::with('kodeMaterialSIM')->get();

        // dd($kodeitem[0]->kodeMaterialSIM->Name);

        return view('main.tools.page-report-po', compact('kodeitem'));
    }

    public function fetchReportPO(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        // Get the start and end dates from the request
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $materialCodes = $request->input('kode_material', []); // Default to empty array if not selected

        // dd($materialCodes);
        // Query purchase orders based on the dates and selected material codes
        $poQuery = DB::connection('mysql3')->table('purchaseorderh as p')
            ->leftJoin('purchaseorderd as d', 'd.DocNo', '=', 'p.DocNo')
            ->leftJoin('mastersupplier as s', 's.Code', 'p.supplierCode')
            ->leftJoin('mastermaterial as m', 'm.Code', 'd.materialCode')
            ->whereBetween('p.DocDate', [$tanggalMulai, $tanggalAkhir]) // Use dynamic dates
            ->select('p.*', 'd.*', 's.Name as supplierName', 'm.Name as materialName');

        // If material codes are selected, apply the filter
        if (!empty($materialCodes)) {
            $poQuery->whereIn('d.MaterialCode', $materialCodes);
        } else {
            // You can show an error or return a message if no materials are selected
            return back()->with('error', 'Please select at least one material code.');
        }

        // Get the results
        $po = $poQuery->get();

        $poResults = [];
        foreach ($po as $item) {
            $materialCode = $item->MaterialCode;
            // dd($materialCode);
            // Array to collect RelatedDocNos from purchaseorderpr and goodsreceipth
            $relatedDocNos = [];

            // Check if DocNo exists in goodsreceipth
            $goodsReceiptDocNos = DB::connection('mysql3')->table('goodsreceipth')
                ->where('PODocNo', '=', $item->DocNo)
                ->pluck('DocNo'); // Get all relevant DocNos from goodsreceipth

            // Check if DocNo exists in purchaseorderpr
            $purchaseOrderPRDocNos = DB::connection('mysql3')->table('purchaseorderpr')
                ->where('DocNo', '=', $item->DocNo)
                ->pluck('PRDocNo'); // Get all relevant PRDocNos from purchaseorderpr

            // Merge results from goodsreceipth and purchaseorderpr
            $relatedDocNos = array_merge($relatedDocNos, $goodsReceiptDocNos->toArray(), $purchaseOrderPRDocNos->toArray());

            // Loop through each RelatedDocNo and create a new row for each result
            foreach ($relatedDocNos as $relatedDocNo) {
                // dd($materialCode);
                // dd($relatedDocNo);
                // Default values for RelatedQty and RelatedDocDate
                $relatedQty = null;
                $relatedDocDate = null;

                // If RelatedDocNo starts with PQC, find in goodsreceiptd and goodsreceipth
                if (strpos($relatedDocNo, 'PQC') === 0) {
                    // Get RelatedQty from goodsreceiptd and DocDate from goodsreceipth
                    $relatedQty = DB::connection('mysql3')->table('goodsreceiptd')
                        ->where('DocNo', '=', $relatedDocNo)
                        ->where('MaterialCode', '=', $materialCode) // Apply MaterialCode filter here
                        ->value('Qty'); // Get total quantity related

                    $relatedDocDate = DB::connection('mysql3')->table('goodsreceipth')
                        ->where('DocNo', '=', $relatedDocNo)
                        // ->where('MaterialCode', '=', $materialCode) // Apply MaterialCode filter here
                        ->value('DocDate'); // Get DocDate from goodsreceipth
                    $relatedUnit = DB::connection('mysql3')->table('goodsreceiptd')
                        ->where('DocNo', '=', $relatedDocNo)
                        ->where('MaterialCode', '=', $materialCode) // Apply MaterialCode filter here
                        ->value('Unit'); // Get DocDate from goodsreceipth
                }
                // If RelatedDocNo starts with PRQ, find in purchaserequestd and purchaserequesth
                elseif (strpos($relatedDocNo, 'PRQ') === 0) {
                    // Get RelatedQty from purchaserequestd and DocDate from purchaserequesth
                    $relatedQty = DB::connection('mysql3')->table('purchaserequestd')
                        ->where('DocNo', '=', $relatedDocNo)
                        ->where('MaterialCode', '=', $materialCode) // Apply MaterialCode filter here
                        ->value('Qty'); // Get total quantity related
                    // dd($relatedQty);
                    $relatedUnit = DB::connection('mysql3')->table('purchaserequestd')
                        ->where('DocNo', '=', $relatedDocNo)
                        ->where('MaterialCode', '=', $materialCode) // Apply MaterialCode filter here
                        ->value('Unit'); // Get total quantity related

                    $relatedDocDate = DB::connection('mysql3')->table('purchaserequesth')
                        ->where('DocNo', '=', $relatedDocNo)
                        // ->where('MaterialCode', '=', $materialCode) // Apply MaterialCode filter here
                        ->value('DocDate'); // Get DocDate from purchaserequesth
                }

                // Add the result to the final array
                $poResults[] = [
                    'DocNo'        => $item->DocNo,
                    'DocDate'      => $item->DocDate,
                    'SupplierCode' => $item->SupplierCode,
                    'SupplierName' => $item->supplierName,
                    'Status'       => $item->Status,
                    'Information'  => $item->Information,
                    'MaterialCode' => $item->MaterialCode,
                    'MaterialName' => $item->materialName,
                    'Info'         => $item->Info,
                    'NameInfo'     => $item->materialName,
                    'Unit'         => $item->Unit,
                    'Qty'          => $item->Qty,
                    'QtyReceived'  => $item->QtyReceived,
                    'QtyRemain'    => $item->Qty - $item->QtyReceived,
                    'RelatedDocNo' => $relatedDocNo, // Each row has one RelatedDocNo
                    'RelatedDocDate' => $relatedDocDate, // DocDate for RelatedDocNo
                    'RelatedQty'   => $relatedQty,   // RelatedQty calculated based on conditions
                    'RelatedUnit' => $relatedUnit, // DocDate for RelatedDocNo
                ];
            }
        }

        // Sort by RelatedDocDate (oldest first)
        usort($poResults, function ($a, $b) {
            return strtotime($a['RelatedDocDate']) - strtotime($b['RelatedDocDate']);
        });

        // Create the Excel file and save it to storage
        try {
            $fileName = 'detail_purchase_orders.xlsx';
            $filePath = storage_path('app/public/' . $fileName);

            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Define the headers (First row)
            $headers = [
                'DocNo',
                'DocDate',
                'SupplierCode',
                'SupplierName',
                'Status',
                'Information',
                'MaterialCode',
                'Name',
                'Info',
                'Name Info',
                'Unit',
                'Qty',
                'QtyReceived',
                'QtyRemain',
                'RelatedDocNo',
                'RelatedDocDate',
                'RelatedQty',
                'RelatedUnit'
            ];

            // Set headers in the first row (row 1)
            $columnIndex = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($columnIndex++, 1, $header);
            }

            // Add data to the sheet (starting from row 2)
            $rowIndex = 2;
            foreach ($poResults as $row) {
                $sheet->setCellValue('A' . $rowIndex, $row['DocNo']);
                $sheet->setCellValue('B' . $rowIndex, $row['DocDate']);
                $sheet->setCellValue('C' . $rowIndex, $row['SupplierCode']);
                $sheet->setCellValue('D' . $rowIndex, $row['SupplierName']);
                $sheet->setCellValue('E' . $rowIndex, $row['Status']);
                $sheet->setCellValue('F' . $rowIndex, $row['Information']);
                $sheet->setCellValue('G' . $rowIndex, $row['MaterialCode']);
                $sheet->setCellValue('H' . $rowIndex, $row['MaterialName']);
                $sheet->setCellValue('I' . $rowIndex, $row['Info']);
                $sheet->setCellValue('J' . $rowIndex, $row['NameInfo']);
                $sheet->setCellValue('K' . $rowIndex, $row['Unit']);
                $sheet->setCellValue('L' . $rowIndex, $row['Qty']);
                $sheet->setCellValue('M' . $rowIndex, $row['QtyReceived']);
                $sheet->setCellValue('N' . $rowIndex, $row['QtyRemain']);
                $sheet->setCellValue('O' . $rowIndex, $row['RelatedDocNo']);
                $sheet->setCellValue('P' . $rowIndex, $row['RelatedDocDate']);
                $sheet->setCellValue('Q' . $rowIndex, $row['RelatedQty']);
                $sheet->setCellValue('R' . $rowIndex, $row['RelatedUnit']);
                $rowIndex++;
            }

            // Write the file to storage
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            // Log the file path to check if the file was stored correctly
            Log::info('File stored at: ' . $filePath);

            // Provide a download link
            return back()->with('success', 'Report berhasil didapatkan!')->with('downloadLink', asset('storage/' . $fileName));
        } catch (\Exception $e) {
            // Log the error if something went wrong
            Log::error('Error saving the file: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan file. Silakan coba lagi.');
        }
    }

    public function indexToolsInvStock(){
        return view('main.tools.page-inventory-stock-calc');
    }


}
