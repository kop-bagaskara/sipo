<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderFukumiController extends Controller
{
    /**
     * Display the Order Fukumi page
     */
    public function index()
    {
        return view('main.order-fukumi.index');
    }

    /**
     * Generate random codes
     */
    public function generateCodes(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:1000000'
        ]);

        $quantity = $request->input('quantity', 100);
        $codes = [];

        for ($i = 0; $i < $quantity; $i++) {
            $codes[] = $this->generateRandomCode();
        }

        return response()->json([
            'success' => true,
            'codes' => $codes,
            'count' => count($codes)
        ]);
    }

    /**
     * Generate and export directly (for large quantities)
     * Optimized for memory efficiency
     */
    public function generateAndExport(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10000000'
        ]);

        $quantity = $request->input('quantity', 100);

        // Increase memory limit temporarily for large exports
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 600); // 10 minutes

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('SIPO System')
            ->setTitle('Order Fukumi - Kode Acak')
            ->setDescription('Generated random codes for Order Fukumi');

        $sheet = $spreadsheet->getActiveSheet();

        // Disable calculation to save memory
        $spreadsheet->getCalculationEngine()->disableCalculationCache();
        \PhpOffice\PhpSpreadsheet\Calculation\Calculation::getInstance($spreadsheet)->disableCalculationCache();

        // Set title
        $sheet->setCellValue('A1', 'Order Fukumi - Kode Acak');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Kode Acak');

        // Style headers only (don't style all rows to save memory)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A3:B3')->applyFromArray($headerStyle);

        // Set column widths (do this before writing data)
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Write data directly without storing in array (memory efficient)
        $row = 4;

        for ($i = 0; $i < $quantity; $i++) {
            // Generate and write directly to sheet (don't store in array)
            $code = $this->generateRandomCode();
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $code);
            $row++;

            // Force garbage collection every 10000 rows to free memory
            if ($i > 0 && $i % 10000 == 0) {
                gc_collect_cycles();
            }
        }

        // Create writer with memory-saving options
        $writer = new Xlsx($spreadsheet);

        // Save to temporary file
        $filename = 'order_fukumi_codes_' . date('Y-m-d_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), 'fukumi_');

        try {
            $writer->save($temp_file);
        } catch (\Exception $e) {
            // Clean up on error
            if (file_exists($temp_file)) {
                unlink($temp_file);
            }
            throw $e;
        }

        // Clear spreadsheet from memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Export codes to Excel
     */
    public function exportToExcel(Request $request)
    {
        $request->validate([
            'codes' => 'required|array',
            'codes.*' => 'string|size:10'
        ]);

        $codes = $request->input('codes', []);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Order Fukumi - Kode Acak');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Kode Acak');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A3:B3')->applyFromArray($headerStyle);

        // Set data
        $row = 4;
        foreach ($codes as $index => $code) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $code);
            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(20);

        // Center align the number column
        $sheet->getStyle('A4:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Create writer and save
        $writer = new Xlsx($spreadsheet);
        $filename = 'order_fukumi_codes_' . date('Y-m-d_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Generate a random 10-character code (alphanumeric)
     */
    private function generateRandomCode()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        $length = 10;

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }
}

