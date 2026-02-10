<?php

namespace App\Imports;

use App\Models\Forecast;
use App\Models\ForecastItem;
use App\Models\ForecastWeeklyData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class ForecastImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    protected $customerName;
    protected $periodMonth;
    protected $periodYear;
    protected $forecast;
    protected $errors = [];
    protected $successCount = 0;

    public function __construct($customerName, $periodMonth, $periodYear)
    {
        $this->customerName = $customerName;
        $this->periodMonth = $periodMonth;
        $this->periodYear = $periodYear;
    }

    public function model(array $row)
    {
        // Create forecast on first row
        if (!$this->forecast) {
            $forecastNumber = Forecast::generateForecastNumber($this->customerName, $this->periodYear);

            $this->forecast = Forecast::create([
                'forecast_number' => $forecastNumber,
                'customer_name' => $this->customerName,
                'period_month' => $this->periodMonth,
                'period_year' => $this->periodYear,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);
        }

        // Skip if item name is empty
        $itemName = $this->getValue($row, 'item_name');
        if (empty($itemName)) {
            return null;
        }

        try {
            // Create forecast item
            $item = ForecastItem::create([
                'forecast_id' => $this->forecast->id,
                'material_code' => $this->getValue($row, 'material_code'),
                'design_code' => $this->getValue($row, 'design_code'),
                'item_name' => $itemName,
                'remarks' => $this->getValue($row, 'remarks'),
                'dpc_group' => $this->getValue($row, 'dpc_group'),
                'sort_order' => $this->successCount,
            ]);

            // Create weekly data (W1 to W5)
            for ($week = 1; $week <= 5; $week++) {
                $qty = $this->getNumericValue($row, "w{$week}_qty");
                $ton = $this->getNumericValue($row, "w{$week}_ton");

                if ($qty !== null || $ton !== null) {
                    ForecastWeeklyData::create([
                        'forecast_item_id' => $item->id,
                        'week_number' => $week,
                        'year' => $this->periodYear,
                        'week_label' => "W{$week}.{$this->periodYear}",
                        'forecast_qty' => $qty,
                        'forecast_ton' => $ton,
                    ]);
                }
            }

            // Update summary
            $item->updateSummary();
            $this->successCount++;

            return null;
        } catch (\Exception $e) {
            $this->errors[] = [
                'row' => $this->successCount + count($this->errors) + 1,
                'item' => $itemName,
                'error' => $e->getMessage()
            ];
            Log::error('Forecast Import Row Error: ' . $e->getMessage());
            return null;
        }
    }

    private function getValue($row, $key)
    {
        // Try different possible column names
        $variations = [
            strtolower($key),
            str_replace('_', ' ', strtolower($key)),
            str_replace(' ', '_', strtolower($key)),
            ucwords(str_replace('_', ' ', strtolower($key))),
        ];

        foreach ($variations as $variation) {
            if (isset($row[$variation])) {
                $value = $row[$variation];
                return is_string($value) ? trim($value) : $value;
            }
        }

        return null;
    }

    private function getNumericValue($row, $key)
    {
        $value = $this->getValue($row, $key);

        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        // Remove commas and convert to float
        $value = str_replace(',', '', $value);
        $numeric = is_numeric($value) ? (float)$value : null;

        return $numeric;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}

