<?php

namespace App\Imports;

use App\Models\Forecast;
use App\Models\ForecastItem;
use App\Models\ForecastWeeklyData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ForecastMultiSheetImport
{

    /**
     * Process Excel file with multiple sheets
     * @param string $filePath Path to Excel file
     * @param string|null $overrideCustomer Override customer from form (optional)
     */
    public static function processFile($filePath, $overrideCustomer = null)
    {
        $results = [];
        $errors = [];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetNames = $spreadsheet->getSheetNames();

            // Filter sheets that start with "FC"
            $fcSheets = array_filter($sheetNames, function($name) {
                return strpos(strtoupper(trim($name)), 'FC') === 0;
            });

            foreach ($fcSheets as $sheetName) {
                try {
                    $sheet = $spreadsheet->getSheetByName($sheetName);

                    // Parse customer and period from sheet name
                    // Format: "FC Unilever Januari 2026" or "FC Unilever Januari 2026"
                    $parsed = self::parseSheetName($sheetName);

                    if (!$parsed) {
                        $errors[] = [
                            'sheet' => $sheetName,
                            'error' => 'Tidak dapat memparse nama sheet'
                        ];
                        continue;
                    }

                    // Override customer if provided from form
                    if ($overrideCustomer) {
                        $parsed['customer'] = $overrideCustomer;
                    }

                    // Find header row (might not be row 1)
                    $headerRowNum = self::findHeaderRow($sheet);

                    // Read header row to find month columns and forecast columns
                    $headerRow = $sheet->getRowIterator($headerRowNum, $headerRowNum)->current();
                    $monthColumns = self::findMonthColumns($sheet, $headerRow, $headerRowNum);
                    $forecastCols = self::findForecastColumns($sheet, $headerRowNum);

                    Log::info("Forecast Import - Sheet: {$sheetName}", [
                        'header_row' => $headerRowNum,
                        'month_columns_found' => count($monthColumns),
                        'forecast_qty_col' => $forecastCols['qty_col'],
                        'forecast_ton_col' => $forecastCols['ton_col'],
                        'highest_row' => $sheet->getHighestRow(),
                        'highest_col' => $sheet->getHighestColumn()
                    ]);

                    // Process data rows (will use both monthColumns and forecastCols)
                    $sheetData = self::processSheetData($sheet, $monthColumns, $forecastCols, $parsed, $headerRowNum);

                    Log::info("Forecast Import - Sheet processed", [
                        'sheet' => $sheetName,
                        'items_found' => count($sheetData['items']),
                        'errors' => count($sheetData['errors'])
                    ]);

                    $results[] = [
                        'sheet_name' => $sheetName,
                        'customer' => $parsed['customer'],
                        'period_month' => $parsed['month'],
                        'period_year' => $parsed['year'],
                        'data' => $sheetData['items'],
                        'item_count' => count($sheetData['items']),
                        'errors' => $sheetData['errors']
                    ];

                } catch (\Exception $e) {
                    $errors[] = [
                        'sheet' => $sheetName,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Error processing sheet {$sheetName}: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            Log::error('Forecast Multi Sheet Import Error: ' . $e->getMessage());
            throw $e;
        }

        return [
            'results' => $results,
            'errors' => $errors
        ];
    }

    /**
     * Parse sheet name to extract customer, month, and year
     * Format: "FC Unilever Januari 2026" -> customer: "Unilever", month: "Januari", year: "2026"
     */
    private static function parseSheetName($sheetName)
    {
        // Remove "FC" prefix and trim
        $name = trim(str_replace('FC', '', $sheetName));

        // Split by space
        $parts = preg_split('/\s+/', $name);

        if (count($parts) < 2) {
            return null;
        }

        // Customer is everything before the last 2 parts (month and year)
        $customer = implode(' ', array_slice($parts, 0, -2));

        // Month is second to last
        $month = $parts[count($parts) - 2] ?? null;

        // Year is last
        $year = $parts[count($parts) - 1] ?? null;

        // Validate month
        $validMonths = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        if (!in_array($month, $validMonths)) {
            return null;
        }

        // Validate year
        if (!is_numeric($year) || strlen($year) != 4) {
            return null;
        }

        return [
            'customer' => $customer,
            'month' => $month,
            'year' => (int)$year
        ];
    }

    /**
     * Find columns that contain month names in header row
     */
    private static function findMonthColumns($sheet, $headerRow, $headerRowNum = 1)
    {
        $monthColumns = [];
        $validMonths = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        // Scan all columns in header row
        for ($colIndex = 1; $colIndex <= $highestColIndex; $colIndex++) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $cell = $sheet->getCell($col . $headerRowNum);
            $value = $cell->getValue();

            if (is_string($value)) {
                $value = trim($value);

                // Check if value is a valid month name
                foreach ($validMonths as $month) {
                    if (stripos($value, $month) !== false) {
                        // Find QTY and TON columns in "Forecast" section
                        // Usually after the week columns
                        $qtyCol = self::findForecastColumn($sheet, $colIndex, 'QTY', $headerRowNum);
                        $tonCol = self::findForecastColumn($sheet, $colIndex, 'TON', $headerRowNum);

                        $monthColumns[$month] = [
                            'month_col' => $col,
                            'month_col_index' => $colIndex,
                            'qty_col' => $qtyCol,
                            'ton_col' => $tonCol,
                        ];
                        break;
                    }
                }
            }
        }

        return $monthColumns;
    }

    /**
     * Find Forecast QTY or TON column after month column
     */
    private static function findForecastColumn($sheet, $startColIndex, $label, $headerRowNum = 1)
    {
        // Search in next 30 columns for "Forecast" section
        for ($i = 0; $i < 30; $i++) {
            $colIndex = $startColIndex + $i;
            $col = Coordinate::stringFromColumnIndex($colIndex);

            // Check header row
            $headerValue = trim($sheet->getCell($col . $headerRowNum)->getValue() ?? '');

            // Look for "Forecast" followed by QTY or TON
            if (stripos($headerValue, 'Forecast') !== false) {
                // Check if next column has QTY or TON
                $nextCol = Coordinate::stringFromColumnIndex($colIndex + 1);
                $nextValue = trim($sheet->getCell($nextCol . $headerRowNum)->getValue() ?? '');

                if (stripos($nextValue, $label) !== false) {
                    return $nextCol;
                }
            }

            // Or direct QTY/TON in Forecast column
            if (stripos($headerValue, 'Forecast') !== false && stripos($headerValue, $label) !== false) {
                return $col;
            }
        }

        return null;
    }


    /**
     * Find Forecast QTY/TON columns - SIMPLE VERSION
     * Cari kolom "Forecast" lalu cari "QTY" dan "TON" di bawahnya
     * Format Excel: Row 2 = "Forecast" (merged), Row 3 = "QTY" dan "TON"
     */
    private static function findForecastColumns($sheet, $headerRow = 1)
    {
        $forecastCols = ['qty_col' => null, 'ton_col' => null];
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        // Cari header "Forecast" di row 1-3 (bisa merged cell)
        $forecastHeaderCol = null;
        $forecastHeaderRow = null;
        for ($row = 1; $row <= 3; $row++) {
            for ($colIndex = 1; $colIndex <= $highestColIndex; $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $headerValue = trim($sheet->getCell($col . $row)->getValue() ?? '');

                if (stripos($headerValue, 'Forecast') !== false && stripos($headerValue, 'QTY') === false && stripos($headerValue, 'TON') === false) {
                    $forecastHeaderCol = $colIndex;
                    $forecastHeaderRow = $row;
                    break 2; // Break both loops
                }
            }
        }

        // Jika ketemu "Forecast", cari "QTY" dan "TON" di row berikutnya (biasanya row 3)
        if ($forecastHeaderCol && $forecastHeaderRow) {
            // Cari di row setelah Forecast header (biasanya row 3)
            $searchRow = $forecastHeaderRow + 1;
            if ($searchRow > 3) $searchRow = 3; // Pastikan tidak lebih dari row 3

            // Cek kolom di sekitar Forecast header (max 5 kolom setelahnya)
            for ($i = 0; $i <= 5; $i++) {
                $colIndex = $forecastHeaderCol + $i;
                if ($colIndex > $highestColIndex) break;

                $col = Coordinate::stringFromColumnIndex($colIndex);
                $headerValue = trim($sheet->getCell($col . $searchRow)->getValue() ?? '');

                // Cari yang persis "QTY" atau "TON" (bukan "W1 QTY" atau lainnya)
                if ((stripos($headerValue, 'QTY') !== false || $headerValue == 'QTY') &&
                    stripos($headerValue, 'W') === false &&
                    !$forecastCols['qty_col']) {
                    $forecastCols['qty_col'] = $col;
                }
                if ((stripos($headerValue, 'TON') !== false || $headerValue == 'TON') &&
                    stripos($headerValue, 'W') === false &&
                    !$forecastCols['ton_col']) {
                    $forecastCols['ton_col'] = $col;
                }
            }
        }

        // Fallback: cari langsung semua kolom yang ada "QTY" dan "TON" di row 3 (tapi bukan weekly)
        if (!$forecastCols['qty_col'] || !$forecastCols['ton_col']) {
            // Prioritas cari di row 3 dulu
            for ($colIndex = 1; $colIndex <= $highestColIndex; $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $headerValue = trim($sheet->getCell($col . '3')->getValue() ?? '');

                // Skip jika ada "W" (weekly data)
                if (stripos($headerValue, 'W') !== false) {
                    continue;
                }

                // Cek apakah ada "Forecast" di row 2 (kolom yang sama atau sebelumnya)
                $hasForecast = false;
                for ($checkCol = max(1, $colIndex - 3); $checkCol <= $colIndex; $checkCol++) {
                    $checkColStr = Coordinate::stringFromColumnIndex($checkCol);
                    for ($checkRow = 1; $checkRow <= 2; $checkRow++) {
                        $checkValue = trim($sheet->getCell($checkColStr . $checkRow)->getValue() ?? '');
                        if (stripos($checkValue, 'Forecast') !== false && stripos($checkValue, 'QTY') === false && stripos($checkValue, 'TON') === false) {
                            $hasForecast = true;
                            break 2;
                        }
                    }
                }

                if ($hasForecast) {
                    if ((stripos($headerValue, 'QTY') !== false || $headerValue == 'QTY') && !$forecastCols['qty_col']) {
                        $forecastCols['qty_col'] = $col;
                    }
                    if ((stripos($headerValue, 'TON') !== false || $headerValue == 'TON') && !$forecastCols['ton_col']) {
                        $forecastCols['ton_col'] = $col;
                    }
                }
            }
        }

        // Log hasil pencarian
        Log::info("Forecast Import - Forecast columns search result", [
            'forecast_header_row' => $forecastHeaderRow,
            'forecast_header_col' => $forecastHeaderCol ? Coordinate::stringFromColumnIndex($forecastHeaderCol) : null,
            'qty_col' => $forecastCols['qty_col'],
            'ton_col' => $forecastCols['ton_col']
        ]);

        return $forecastCols;
    }

    /**
     * Find header row (search first 5 rows)
     */
    private static function findHeaderRow($sheet)
    {
        // Check first 5 rows for header indicators
        for ($row = 1; $row <= 5; $row++) {
            // Check if row contains "Item" or month names
            $highestCol = $sheet->getHighestColumn();
            $highestColIndex = Coordinate::columnIndexFromString($highestCol);

            for ($colIndex = 1; $colIndex <= min(10, $highestColIndex); $colIndex++) {
                $col = Coordinate::stringFromColumnIndex($colIndex);
                $cellValue = trim($sheet->getCell($col . $row)->getValue() ?? '');

                if (stripos($cellValue, 'Item') !== false ||
                    in_array($cellValue, ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                         'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'])) {
                    return $row;
                }
            }
        }

        return 1; // Default to row 1
    }

    /**
     * Find item name and code columns dynamically
     */
    private static function findItemColumns($sheet, $headerRow = 1)
    {
        $itemCols = ['code_col' => 'C', 'name_col' => 'D']; // Default

        // Try to find "Item" column in header
        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        for ($colIndex = 1; $colIndex <= min(10, $highestColIndex); $colIndex++) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $headerValue = trim($sheet->getCell($col . $headerRow)->getValue() ?? '');

            if (stripos($headerValue, 'Item') !== false) {
                $itemCols['name_col'] = $col;
                // Code is usually one column before (C for code, D for name)
                if ($colIndex > 1) {
                    $itemCols['code_col'] = Coordinate::stringFromColumnIndex($colIndex - 1);
                }
                break;
            }
        }

        return $itemCols;
    }

    /**
     * Process sheet data rows
     */
    private static function processSheetData($sheet, $monthColumns, $forecastCols, $parsed, $headerRow = 1)
    {
        $items = [];
        $errors = [];
        $highestRow = $sheet->getHighestRow();

        // Find item columns dynamically
        $itemCols = self::findItemColumns($sheet, $headerRow);
        $itemCodeCol = $itemCols['code_col'];
        $itemNameCol = $itemCols['name_col'];

        // Log Forecast columns yang ditemukan
        Log::info("Forecast Import - Forecast columns found", [
            'qty_col' => $forecastCols['qty_col'],
            'ton_col' => $forecastCols['ton_col'],
            'header_row' => $headerRow
        ]);

        // Start from row after header
        $startRow = $headerRow + 1;
        for ($row = $startRow; $row <= $highestRow; $row++) {
            try {
                // Get cell values
                $itemCodeCell = $sheet->getCell($itemCodeCol . $row);
                $itemNameCell = $sheet->getCell($itemNameCol . $row);

                $itemCode = $itemCodeCell->getValue();
                $itemName = $itemNameCell->getValue();

                // Convert to string and trim - handle different data types
                if ($itemCode !== null) {
                    if (is_numeric($itemCode)) {
                        $itemCode = (string)$itemCode;
                    } else {
                        $itemCode = trim((string)$itemCode);
                    }
                } else {
                    $itemCode = '';
                }

                if ($itemName !== null) {
                    if (is_numeric($itemName)) {
                        $itemName = (string)$itemName;
                    } else {
                        $itemName = trim((string)$itemName);
                    }
                } else {
                    $itemName = '';
                }

                // Skip empty rows (both code and name empty)
                if (empty($itemName) && empty($itemCode)) {
                    continue;
                }

                // If item name is empty but code exists, use code as name
                if (empty($itemName) && !empty($itemCode)) {
                    $itemName = $itemCode;
                }

                // Skip if still empty
                if (empty($itemName)) {
                    continue;
                }

                $item = [
                    'material_code' => $itemCode ?: null,
                    'item_name' => $itemName,
                    'design_code' => null,
                    'dpc_group' => null,
                    'remarks' => null,
                    'weekly_data' => [],
                    'forecast_qty' => 0,
                    'forecast_ton' => 0
                ];

                // AMBIL NILAI FORECAST QTY DAN TON SAJA - IGNORE WEEKLY DATA
                if ($forecastCols['qty_col'] && $forecastCols['ton_col']) {
                    $qtyCellAddress = $forecastCols['qty_col'] . $row;
                    $tonCellAddress = $forecastCols['ton_col'] . $row;

                    $qtyCell = $sheet->getCell($qtyCellAddress);
                    $tonCell = $sheet->getCell($tonCellAddress);

                    // Ambil calculated value (bukan formula)
                    $qtyRaw = $qtyCell->getCalculatedValue();
                    $tonRaw = $tonCell->getCalculatedValue();

                    // Log raw values untuk debugging
                    if ($row <= $startRow + 3) {
                        Log::debug("Forecast Import - Row {$row} Raw cell values", [
                            'item_name' => $itemName,
                            'qty_cell' => $qtyCellAddress,
                            'qty_raw' => $qtyRaw,
                            'qty_type' => gettype($qtyRaw),
                            'ton_cell' => $tonCellAddress,
                            'ton_raw' => $tonRaw,
                            'ton_type' => gettype($tonRaw)
                        ]);
                    }

                    $qty = self::getNumericValue($qtyRaw);
                    $ton = self::getNumericValue($tonRaw);

                    if ($qty !== null) {
                        $item['forecast_qty'] = $qty;
                    }
                    if ($ton !== null) {
                        $item['forecast_ton'] = $ton;
                    }

                    // Log untuk debugging
                    if ($row <= $startRow + 3) {
                        Log::debug("Forecast Import - Row {$row} Parsed Forecast values", [
                            'item_name' => $itemName,
                            'qty_parsed' => $qty,
                            'ton_parsed' => $ton
                        ]);
                    }
                } else {
                    // Log jika kolom tidak ditemukan
                    if ($row == $startRow) {
                        Log::warning("Forecast Import - Forecast columns not found", [
                            'qty_col' => $forecastCols['qty_col'],
                            'ton_col' => $forecastCols['ton_col']
                        ]);
                    }
                }

                // Always add item if it has item name (for preview)
                // This ensures all items are shown in preview, even if forecast data is 0
                $items[] = $item;

                Log::debug("Forecast Import - Item added", [
                    'row' => $row,
                    'item_name' => $itemName,
                    'forecast_qty' => $item['forecast_qty'],
                    'forecast_ton' => $item['forecast_ton'],
                    'weekly_data_count' => count($item['weekly_data'])
                ]);

            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $row,
                    'error' => $e->getMessage()
                ];
                Log::error('Error processing row ' . $row . ': ' . $e->getMessage());
            }
        }

        return [
            'items' => $items,
            'errors' => $errors
        ];
    }

    /**
     * Extract week data from columns (W6, W7, W8, etc.)
     */
    private static function extractWeekData($sheet, $row, $startCol)
    {
        $weekData = [];
        $startColIndex = is_numeric($startCol) ? $startCol : Coordinate::columnIndexFromString($startCol);

        // Search for week columns (W6, W7, W8, W9, W10, etc.) in next 20 columns
        for ($i = 0; $i < 20; $i++) {
            $colIndex = $startColIndex + $i;
            $col = Coordinate::stringFromColumnIndex($colIndex);

            // Check header for week label (W6.2025, W7.2025, etc.)
            // Try to find header row dynamically
            $headerRow = 1;
            for ($h = 1; $h <= 3; $h++) {
                $testHeader = trim($sheet->getCell($col . $h)->getValue() ?? '');
                if (preg_match('/W\d+/i', $testHeader)) {
                    $headerRow = $h;
                    break;
                }
            }
            $headerValue = trim($sheet->getCell($col . $headerRow)->getValue() ?? '');

            if (preg_match('/W(\d+)\.?(\d{4})?/i', $headerValue, $matches)) {
                $weekNumber = (int)$matches[1];
                $year = isset($matches[2]) ? (int)$matches[2] : date('Y');

                // Get QTY value from this column
                $qty = self::getNumericValue($sheet->getCell($col . $row)->getValue());

                // TON might be in same column or next column - check both
                $ton = null;
                // Try next column first
                $nextCol = Coordinate::stringFromColumnIndex($colIndex + 1);
                $nextHeader = trim($sheet->getCell($nextCol . '1')->getValue() ?? '');
                if (stripos($nextHeader, 'TON') !== false || is_numeric($sheet->getCell($nextCol . $row)->getValue())) {
                    $ton = self::getNumericValue($sheet->getCell($nextCol . $row)->getValue());
                }

                if ($qty !== null || $ton !== null) {
                    $weekData[] = [
                        'week_number' => $weekNumber,
                        'year' => $year,
                        'week_label' => "W{$weekNumber}.{$year}",
                        'forecast_qty' => $qty,
                        'forecast_ton' => $ton,
                    ];
                }
            }
        }

        return $weekData;
    }

    /**
     * Get numeric value from cell
     */
    private static function getNumericValue($value)
    {
        if ($value === null || $value === '' || $value === '-' || $value === '#N/A') {
            return null;
        }

        // Jika sudah numeric, langsung return
        if (is_numeric($value)) {
            return (float)$value;
        }

        // Convert ke string
        $value = (string)$value;

        // Hapus whitespace
        $value = trim($value);

        // Skip jika kosong atau error
        if (empty($value) || stripos($value, '#N/A') !== false || stripos($value, '#REF') !== false) {
            return null;
        }

        // Hapus koma sebagai pemisah ribuan (format Indonesia: 1.000.000,50)
        // Tapi hati-hati dengan format desimal yang pakai koma
        // Cek apakah ada titik sebagai pemisah ribuan (format US: 1,000,000.50)
        if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
            // Format US: 1,000,000.50 -> hapus koma, biarkan titik
            $value = str_replace(',', '', $value);
        } else {
            // Format Indonesia: 1.000.000,50 -> ganti titik jadi kosong, koma jadi titik
            // Atau format tanpa desimal: 1.000.000 -> hapus titik
            $lastComma = strrpos($value, ',');
            $lastDot = strrpos($value, '.');

            if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
                // Koma adalah desimal, titik adalah ribuan
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else if ($lastDot !== false && ($lastComma === false || $lastDot > $lastComma)) {
                // Titik adalah desimal, koma adalah ribuan (atau tidak ada koma)
                $value = str_replace(',', '', $value);
            } else {
                // Hapus semua koma dan titik, anggap sebagai pemisah ribuan
                $value = str_replace([',', '.'], '', $value);
            }
        }

        // Hapus karakter non-numeric kecuali titik dan minus
        $value = preg_replace('/[^\d.\-]/', '', $value);

        return is_numeric($value) ? (float)$value : null;
    }
}

