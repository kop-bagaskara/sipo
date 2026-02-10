<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\PelumasanMaintenance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Get lubrication schedule data for timeline table
     * Filtered by month and after plan dates
     */
    public function getLubricationForTimeline(Request $request): JsonResponse
    {
        try {
            // Get today's date
            $today = Carbon::now()->startOfDay();

            // Get date 7 days from now
            $sevenDaysFromNow = Carbon::now()->addDays(7)->endOfDay();

            // Get lubrication data for next 7 days
            $lubricationData = DB::connection('mysql2')->table('tpenjadwalan')->whereBetween('datetgs', [
                $today->format('Y-m-d'),
                $sevenDaysFromNow->format('Y-m-d')
            ])
                ->where('status_tugas', '!=', 'completed') // Exclude completed tasks
                ->orderBy('datetgs', 'asc')
                ->get();

            // Transform data for timeline display
            $transformedData = $lubricationData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title ?? 'MAINT-' . $item->id,
                    'datetgs' => $item->datetgs,
                    'isi_tugas' => $item->isi_tugas ?? 'Maintenance Task',
                    'mesin' => $item->mesin,
                    'pelumasan_shift' => $item->pelumasan_shift ?? 'Shift 1',
                    'status_tugas' => $item->status_tugas ?? 'pending',
                    'durasi' => $item->durasi ?? 2, // Default 2 hours
                    'teknisi' => $item->teknisi ?? 'Maintenance Team',
                    'jenis_maintenance' => $item->jenis_maintenance ?? 'Pelumasan',
                    'is_maintenance' => true,
                    'process' => 'Maintenance',
                    'code_item' => 'MAINTENANCE',
                    'material_name' => $item->isi_tugas ?? 'Maintenance Schedule',
                    'wo_docno' => $item->title ?? 'MAINT-' . $item->id,
                    'quantity' => 1,
                    'delivery_date' => $item->datetgs,
                    'capacity' => 10000,
                    'flag_status' => 'pending'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'period' => $today->format('d M Y') . ' - ' . $sevenDaysFromNow->format('d M Y'),
                'total_items' => $transformedData->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching lubrication data: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get lubrication data for specific machine
     */
    public function getLubricationByMachine(Request $request, string $machineCode): JsonResponse
    {
        try {
            // Get today's date
            $today = Carbon::now()->startOfDay();

            // Get date 7 days from now
            $sevenDaysFromNow = Carbon::now()->addDays(7)->endOfDay();

            $lubricationData = DB::connection('mysql2')->table('tpenjadwalan')
                ->leftJoin('tmesin', 'tmesin.namaMesin', '=', 'tpenjadwalan.mesin')
                ->where('tmesin.codeMesinSim', $machineCode)
                ->whereBetween('datetgs', [
                    $today->format('Y-m-d'),
                    $sevenDaysFromNow->format('Y-m-d')
                ])
                ->where('status_tugas', '=', 'PLAN')
                ->orderBy('datetgs', 'asc')
                ->get();

            $transformedData = $lubricationData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title ?? 'MAINT-' . $item->id,
                    'datetgs' => $item->datetgs,
                    'isi_tugas' => $item->isi_tugas ?? 'Maintenance Task',
                    'mesin' => $item->codeMesinSim,
                    'pelumasan_shift' => $item->pelumasan_shift ?? 'Shift 1',
                    'status_tugas' => $item->status_tugas ?? 'pending',
                    'durasi' => $item->durasi ?? 2,
                    'teknisi' => $item->teknisi ?? 'Maintenance Team',
                    'jenis_maintenance' => $item->jenis_maintenance ?? 'Pelumasan',
                    'is_maintenance' => true,
                    'process' => 'Maintenance',
                    'code_item' => 'MAINTENANCE',
                    'material_name' => $item->isi_tugas ?? 'Maintenance Schedule',
                    'wo_docno' => $item->title ?? 'MAINT-' . $item->id,
                    'quantity' => 1,
                    'delivery_date' => $item->datetgs,
                    'capacity' => 10000,
                    'flag_status' => 'pending'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'machine' => $machineCode,
                'period' => $today->format('d M Y') . ' - ' . $sevenDaysFromNow->format('d M Y'),
                'total_items' => $transformedData->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching lubrication data for machine ' . $machineCode . ': ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
