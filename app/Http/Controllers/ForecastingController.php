<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forecast;
use App\Models\ForecastItem;
use App\Models\ForecastWeeklyData;
use App\Imports\ForecastImport;
use App\Imports\ForecastMultiSheetImport;
use App\Exports\ForecastTemplateExport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ForecastingController extends Controller
{
    public function index()
    {
        return view('main.forecasting.index');
    }

    public function list()
    {
        return view('main.forecasting.list');
    }

    public function create()
    {
        return view('main.forecasting.form', ['mode' => 'create']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'period_month' => 'required|string',
            'period_year' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate forecast number
            $forecastNumber = Forecast::generateForecastNumber(
                $request->customer_name,
                $request->period_year
            );

            // Create forecast
            $forecast = Forecast::create([
                'forecast_number' => $forecastNumber,
                'customer_name' => $request->customer_name,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            // Create items
            foreach ($request->items as $index => $itemData) {
                $item = ForecastItem::create([
                    'forecast_id' => $forecast->id,
                    'material_code' => $itemData['material_code'] ?? null,
                    'design_code' => $itemData['design_code'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'remarks' => $itemData['remarks'] ?? null,
                    'dpc_group' => $itemData['dpc_group'] ?? null,
                    'sort_order' => $index,
                ]);

                // Create weekly data
                if (isset($itemData['weekly_data'])) {
                    foreach ($itemData['weekly_data'] as $weekData) {
                        ForecastWeeklyData::create([
                            'forecast_item_id' => $item->id,
                            'week_number' => $weekData['week_number'],
                            'year' => $weekData['year'],
                            'week_label' => $weekData['week_label'],
                            'forecast_qty' => $weekData['forecast_qty'] ?? null,
                            'forecast_ton' => $weekData['forecast_ton'] ?? null,
                            'ao_qty' => $weekData['ao_qty'] ?? null,
                            'ao_ton' => $weekData['ao_ton'] ?? null,
                            'sod_qty' => $weekData['sod_qty'] ?? null,
                            'sod_ton' => $weekData['sod_ton'] ?? null,
                        ]);
                    }
                }

                // Update summary
                $item->updateSummary();
            }

            DB::commit();

            return redirect()->route('forecasting.list')
                ->with('success', 'Forecast berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Forecast Store Error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan forecast: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $forecast = Forecast::with(['items.weeklyData'])->findOrFail($id);
        return view('main.forecasting.form', ['mode' => 'edit', 'forecast' => $forecast]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'period_month' => 'required|string',
            'period_year' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
        ]);

        $forecast = Forecast::findOrFail($id);

        DB::beginTransaction();
        try {
            // Update forecast
            $forecast->update([
                'customer_name' => $request->customer_name,
                'period_month' => $request->period_month,
                'period_year' => $request->period_year,
                'notes' => $request->notes,
            ]);

            // Preserve existing item values before deletion (for items without weekly_data)
            $preservedValues = [];
            foreach ($forecast->items as $existingItem) {
                $preservedValues[$existingItem->item_name] = [
                    'forecast_qty' => $existingItem->forecast_qty ?? 0,
                    'forecast_ton' => $existingItem->forecast_ton ?? 0,
                ];
            }

            // Delete existing items and weekly data
            $forecast->items()->each(function($item) {
                $item->weeklyData()->delete();
            });
            $forecast->items()->delete();

            // Create new items
            foreach ($request->items as $index => $itemData) {
                // Check if there's any weekly_data with actual values
                $hasWeeklyData = false;
                if (!empty($itemData['weekly_data']) && is_array($itemData['weekly_data'])) {
                    foreach ($itemData['weekly_data'] as $weekData) {
                        if (!empty($weekData['forecast_qty']) || !empty($weekData['forecast_ton'])) {
                            $hasWeeklyData = true;
                            break;
                        }
                    }
                }

                // Use preserved values if no weekly_data, otherwise use 0 (will be calculated by updateSummary)
                $initialQty = 0;
                $initialTon = 0;
                if (!$hasWeeklyData && isset($preservedValues[$itemData['item_name']])) {
                    $initialQty = $preservedValues[$itemData['item_name']]['forecast_qty'];
                    $initialTon = $preservedValues[$itemData['item_name']]['forecast_ton'];
                }

                $item = ForecastItem::create([
                    'forecast_id' => $forecast->id,
                    'material_code' => $itemData['material_code'] ?? null,
                    'design_code' => $itemData['design_code'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'remarks' => $itemData['remarks'] ?? null,
                    'dpc_group' => $itemData['dpc_group'] ?? null,
                    'forecast_qty' => $initialQty,
                    'forecast_ton' => $initialTon,
                    'sort_order' => $index,
                ]);

                // Create weekly data (jika ada)
                if ($hasWeeklyData) {
                    foreach ($itemData['weekly_data'] as $weekData) {
                        // Skip if both qty and ton are empty
                        if (empty($weekData['forecast_qty']) && empty($weekData['forecast_ton'])) {
                            continue;
                        }

                        ForecastWeeklyData::create([
                            'forecast_item_id' => $item->id,
                            'week_number' => $weekData['week_number'] ?? null,
                            'year' => $weekData['year'] ?? null,
                            'week_label' => $weekData['week_label'] ?? null,
                            'forecast_qty' => $weekData['forecast_qty'] ?? 0,
                            'forecast_ton' => $weekData['forecast_ton'] ?? 0,
                            'ao_qty' => $weekData['ao_qty'] ?? null,
                            'ao_ton' => $weekData['ao_ton'] ?? null,
                            'sod_qty' => $weekData['sod_qty'] ?? null,
                            'sod_ton' => $weekData['sod_ton'] ?? null,
                        ]);
                    }

                    // Update summary hanya jika ada weekly_data yang di-input
                    $item->updateSummary();
                }
                // Jika tidak ada weekly_data, nilai forecast_qty dan forecast_ton sudah di-preserve dari existing item
            }

            DB::commit();

            return redirect()->route('forecasting.list')
                ->with('success', 'Forecast berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Forecast Update Error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate forecast: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $forecast = Forecast::findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete weekly data and items
            $forecast->items()->each(function($item) {
                $item->weeklyData()->delete();
            });
            $forecast->items()->delete();
            $forecast->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Forecast berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Forecast Delete Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus forecast'
            ], 500);
        }
    }

    public function data()
    {
        $forecasts = Forecast::with('creator')->orderBy('created_at', 'desc');

        return DataTables::of($forecasts)
            ->addIndexColumn()
            ->addColumn('forecast_number', function ($row) {
                return $row->forecast_number;
            })
            ->addColumn('customer', function ($row) {
                return $row->customer_name;
            })
            ->addColumn('period', function ($row) {
                return $row->period_month . ' ' . $row->period_year;
            })
            ->addColumn('status', function ($row) {
                $badgeClass = 'badge-secondary';
                if ($row->status === 'draft') {
                    $badgeClass = 'badge-warning';
                } else if ($row->status === 'submitted') {
                    $badgeClass = 'badge-info';
                } else if ($row->status === 'approved') {
                    $badgeClass = 'badge-success';
                } else if ($row->status === 'rejected') {
                    $badgeClass = 'badge-danger';
                }
                return '<span class="badge ' . $badgeClass . '">' . strtoupper($row->status) . '</span>';
            })
            ->addColumn('created_by', function ($row) {
                return $row->creator ? $row->creator->name : '-';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at;
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<a href="' . route('forecasting.edit', $row->id) . '" class="btn btn-sm btn-info" title="Edit"><i class="mdi mdi-pencil"></i></a>';
                $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger delete-forecast" data-id="' . $row->id . '" title="Hapus"><i class="mdi mdi-delete"></i></a>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        try {
            // Save uploaded file temporarily
            $file = $request->file('file');

            // Ensure temp directory exists
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Store file and get full path
            $fileName = 'forecast_import_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('temp', $fileName);

            // Get full path - try Storage::path() first, fallback to storage_path()
            try {
                $fullPath = Storage::path($filePath);
            } catch (\Exception $e) {
                $fullPath = storage_path('app/' . $filePath);
            }

            // Verify file exists
            if (!file_exists($fullPath)) {
                Log::error('Forecast Import - File not found', [
                    'filePath' => $filePath,
                    'fullPath' => $fullPath,
                    'storage_path' => storage_path('app/' . $filePath),
                    'temp_dir_exists' => file_exists($tempDir),
                    'temp_dir_writable' => is_writable($tempDir),
                ]);
                throw new \Exception("File tidak dapat disimpan: {$fullPath}. Path yang dicoba: " . storage_path('app/' . $filePath));
            }

            // Process multi-sheet import (preview only, no save)
            $result = ForecastMultiSheetImport::processFile($fullPath);

            // Organize data for preview
            $previewData = self::organizePreviewData($result['results']);

            return response()->json([
                'success' => true,
                'preview_data' => $previewData,
                'temp_file_path' => $filePath,
                'errors' => $result['errors']
            ]);

        } catch (\Exception $e) {
            Log::error('Forecast Preview Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Organize preview data by customer group
     */
    private static function organizePreviewData($results)
    {
        $organized = [];

        foreach ($results as $sheetResult) {
            $customer = $sheetResult['customer'];
            $periodMonth = $sheetResult['period_month'];
            $periodYear = $sheetResult['period_year'];

            if (!isset($organized[$customer])) {
                $organized[$customer] = [
                    'customer' => $customer,
                    'periods' => [],
                    'total_items' => 0
                ];
            }

            // Group by period (month + year)
            $periodKey = $periodMonth . ' ' . $periodYear;
            if (!isset($organized[$customer]['periods'][$periodKey])) {
                $organized[$customer]['periods'][$periodKey] = [
                    'month' => $periodMonth,
                    'year' => $periodYear,
                    'items' => [],
                    'total_qty' => 0,
                    'total_ton' => 0
                ];
            }

            // Add items with their forecast data
            foreach ($sheetResult['data'] as $item) {
                $itemPreview = [
                    'material_code' => $item['material_code'],
                    'item_name' => $item['item_name'],
                    'design_code' => $item['design_code'],
                    'forecast_qty' => $item['forecast_qty'] ?? 0,
                    'forecast_ton' => $item['forecast_ton'] ?? 0,
                    'weekly_data' => []
                ];

                // Group weekly data by month if available
                if (!empty($item['weekly_data'])) {
                    foreach ($item['weekly_data'] as $week) {
                        $itemPreview['weekly_data'][] = [
                            'week' => $week['week_label'],
                            'qty' => $week['forecast_qty'] ?? 0,
                            'ton' => $week['forecast_ton'] ?? 0,
                        ];
                    }
                }

                $organized[$customer]['periods'][$periodKey]['items'][] = $itemPreview;
                $organized[$customer]['periods'][$periodKey]['total_qty'] += $itemPreview['forecast_qty'];
                $organized[$customer]['periods'][$periodKey]['total_ton'] += $itemPreview['forecast_ton'];
                $organized[$customer]['total_items']++;
            }
        }

        return array_values($organized);
    }

    public function confirmImport(Request $request)
    {
        $request->validate([
            'temp_file_path' => 'required|string',
        ]);

        try {
            // Get full path
            $filePath = $request->temp_file_path;
            try {
                $fullPath = Storage::path($filePath);
            } catch (\Exception $e) {
                $fullPath = storage_path('app/' . $filePath);
            }

            if (!file_exists($fullPath)) {
                throw new \Exception("File tidak ditemukan: {$fullPath}");
            }

            // Process multi-sheet import
            $result = ForecastMultiSheetImport::processFile($fullPath);

            DB::beginTransaction();

            $totalImported = 0;
            $totalErrors = 0;
            $importedForecasts = [];

            foreach ($result['results'] as $sheetResult) {
                try {
                    // Generate forecast number
                    $forecastNumber = Forecast::generateForecastNumber(
                        $sheetResult['customer'],
                        $sheetResult['period_year']
                    );

                    // Create forecast
                    $forecast = Forecast::create([
                        'forecast_number' => $forecastNumber,
                        'customer_name' => $sheetResult['customer'],
                        'period_month' => $sheetResult['period_month'],
                        'period_year' => $sheetResult['period_year'],
                        'status' => 'draft',
                        'created_by' => auth()->id(),
                    ]);

                    Log::info("Forecast Import - Creating forecast", [
                        'forecast_number' => $forecastNumber,
                        'customer' => $sheetResult['customer'],
                        'period' => $sheetResult['period_month'] . ' ' . $sheetResult['period_year'],
                        'items_count' => count($sheetResult['data'])
                    ]);

                    // Create items
                    $itemCount = 0;
                    foreach ($sheetResult['data'] as $index => $itemData) {
                        try {
                            // Validasi item_name wajib ada
                            if (empty($itemData['item_name'])) {
                                Log::warning("Forecast Import - Skipping item with empty name", [
                                    'index' => $index,
                                    'data' => $itemData
                                ]);
                                continue;
                            }

                            $item = ForecastItem::create([
                                'forecast_id' => $forecast->id,
                                'material_code' => $itemData['material_code'] ?? null,
                                'design_code' => $itemData['design_code'] ?? null,
                                'item_name' => $itemData['item_name'],
                                'remarks' => $itemData['remarks'] ?? null,
                                'dpc_group' => $itemData['dpc_group'] ?? null,
                                'forecast_qty' => $itemData['forecast_qty'] ?? 0,
                                'forecast_ton' => $itemData['forecast_ton'] ?? 0,
                                'sort_order' => $index,
                            ]);

                            Log::debug("Forecast Import - Item created", [
                                'item_name' => $itemData['item_name'],
                                'forecast_qty' => $item->forecast_qty,
                                'forecast_ton' => $item->forecast_ton
                            ]);

                            // Create weekly data (jika ada)
                            if (!empty($itemData['weekly_data']) && is_array($itemData['weekly_data'])) {
                                foreach ($itemData['weekly_data'] as $weekData) {
                                    ForecastWeeklyData::create([
                                        'forecast_item_id' => $item->id,
                                        'week_number' => $weekData['week_number'] ?? null,
                                        'year' => $weekData['year'] ?? null,
                                        'week_label' => $weekData['week_label'] ?? null,
                                        'forecast_qty' => $weekData['forecast_qty'] ?? 0,
                                        'forecast_ton' => $weekData['forecast_ton'] ?? 0,
                                    ]);
                                }
                                // Update summary hanya jika ada weekly data
                                $item->updateSummary();
                            }
                            // Jika tidak ada weekly data, nilai forecast_qty dan forecast_ton sudah langsung dari Excel
                            // Tidak perlu updateSummary() karena akan overwrite nilai yang sudah benar

                            $itemCount++;
                            $totalImported++;
                        } catch (\Exception $e) {
                            $totalErrors++;
                            Log::error('Error creating item in sheet ' . $sheetResult['sheet_name'], [
                                'item_data' => $itemData,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                    }

                    $importedForecasts[] = [
                        'forecast_number' => $forecastNumber,
                        'customer' => $sheetResult['customer'],
                        'period' => $sheetResult['period_month'] . ' ' . $sheetResult['period_year'],
                        'item_count' => $itemCount
                    ];

                    Log::info("Forecast Import - Sheet completed", [
                        'forecast_number' => $forecastNumber,
                        'items_saved' => $itemCount
                    ]);

                } catch (\Exception $e) {
                    $totalErrors++;
                    Log::error('Error importing sheet ' . ($sheetResult['sheet_name'] ?? 'unknown'), [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Count errors
            foreach ($result['errors'] as $error) {
                $totalErrors++;
            }

            DB::commit();

            // Clean up temp file
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            $message = "Import berhasil! ";
            $message .= count($importedForecasts) . " forecast diimport dengan total {$totalImported} item";
            if ($totalErrors > 0) {
                $message .= ". Terdapat {$totalErrors} error";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'imported_forecasts' => $importedForecasts,
                    'total_items' => $totalImported,
                    'total_errors' => $totalErrors,
                    'errors' => $result['errors']
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Forecast Import Error: ' . $e->getMessage());

            // Clean up temp file on error
            if (isset($fullPath) && file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }

    // Keep old import method for backward compatibility (deprecated)
    public function import(Request $request)
    {
        return $this->previewImport($request);
    }

    public function downloadTemplate()
    {
        // Create template using Maatwebsite Excel
        $data = [
            ['Material Code', 'Design Code', 'Item Name', 'DPC Group', 'Remarks',
             'W1 QTY', 'W1 TON', 'W2 QTY', 'W2 TON', 'W3 QTY', 'W3 TON',
             'W4 QTY', 'W4 TON', 'W5 QTY', 'W5 TON'],
            ['2025200223', 'DS.0230.0092', 'IB NEXTAR BROWNIES 13PCSX27G', 'DPC 310 42,5 x 83', 'By PO + Forecast',
             '270720', '3', '461376', '3', '133200', '2', '439560', '12', '659340', '27'],
        ];

        return Excel::download(
            new ForecastTemplateExport($data),
            'Forecast_Template.xlsx'
        );
    }
}

