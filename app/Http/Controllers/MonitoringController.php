<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    /**
     * Menampilkan halaman monitoring SO
     */
    public function indexMonitoringSO()
    {
        return view('main.process.monitoring-so');
    }

    /**
     * API untuk mengambil data SO, WO, LPO per bulan
     */
    public function getMonitoringData(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', null);
            $divisi = $request->input('divisi', null);

            // Data untuk 12 bulan
            $months = [];
            $soData = [];
            $woData = [];
            $lpoData = [];

            for ($i = 1; $i <= 12; $i++) {
                $months[] = Carbon::createFromDate($year, $i, 1)->format('M');

                $startDate = Carbon::createFromDate($year, $i, 1)->startOfMonth();
                $endDate = Carbon::createFromDate($year, $i, 1)->endOfMonth();

                // Jika ada filter bulan spesifik, hanya ambil bulan tersebut
                if ($month && $month != $i) {
                    $soData[] = 0;
                    $woData[] = 0;
                    $lpoData[] = 0;
                    continue;
                }

                // Count SO dari tabel salesorderh
                $soCount = DB::connection('mysql3')
                    ->table('salesorderh')
                    ->whereBetween('docdate', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->count();

                // Ambil semua nomor SO untuk bulan ini
                $soNumbers = DB::connection('mysql3')
                    ->table('salesorderh')
                    ->whereBetween('docdate', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->pluck('DocNo')
                    ->toArray();

                // Count WO yang dibuat berdasarkan SO (menggunakan SODocNo)
                $woCount = 0;
                if (!empty($soNumbers)) {
                    $woCount = DB::connection('mysql3')
                        ->table('workorderh')
                        ->whereIn('SODocNo', $soNumbers)
                        ->count();
                }

                // Ambil semua nomor WO yang terkait dengan SO
                $woNumbers = [];
                if (!empty($soNumbers)) {
                    $woNumbers = DB::connection('mysql3')
                        ->table('workorderh')
                        ->whereIn('SODocNo', $soNumbers)
                        ->pluck('DocNo')
                        ->toArray();
                }

                // Count LPO yang dibuat berdasarkan WO (menggunakan DocNo dari WO)
                $lpoCount = 0;
                if (!empty($woNumbers)) {
                    $lpoCount = DB::connection('mysql4')
                        ->table('workorders_local')
                        ->whereIn('DocNo', $woNumbers)
                        ->count();
                }

                $soData[] = $soCount;
                $woData[] = $woCount;
                $lpoData[] = $lpoCount;
            }

            // Jika ada filter bulan spesifik, hanya tampilkan bulan tersebut
            if ($month) {
                $monthIndex = $month - 1;
                $months = [$months[$monthIndex]];
                $soData = [$soData[$monthIndex]];
                $woData = [$woData[$monthIndex]];
                $lpoData = [$lpoData[$monthIndex]];
            }

            // Hitung total
            $totalSO = array_sum($soData);
            $totalWO = array_sum($woData);
            $totalLPO = array_sum($lpoData);

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $months,
                    'datasets' => [
                        [
                            'label' => 'Sales Order (SO)',
                            'data' => $soData,
                            'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                            'borderColor' => 'rgba(54, 162, 235, 1)',
                            'borderWidth' => 2,
                            'borderRadius' => 4,
                            'borderSkipped' => false,
                        ],
                        [
                            'label' => 'Work Order (WO) dari SO',
                            'data' => $woData,
                            'backgroundColor' => 'rgba(75, 192, 192, 0.8)',
                            'borderColor' => 'rgba(75, 192, 192, 1)',
                            'borderWidth' => 2,
                            'borderRadius' => 4,
                            'borderSkipped' => false,
                        ],
                        [
                            'label' => 'Local Purchase Order (LPO) dari WO',
                            'data' => $lpoData,
                            'backgroundColor' => 'rgba(255, 205, 86, 0.8)',
                            'borderColor' => 'rgba(255, 205, 86, 1)',
                            'borderWidth' => 2,
                            'borderRadius' => 4,
                            'borderSkipped' => false,
                        ]
                    ]
                ],
                'summary' => [
                    'totalSO' => $totalSO,
                    'totalWO' => $totalWO,
                    'totalLPO' => $totalLPO
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API untuk mengambil data detail per bulan
     */
    public function getMonthlyDetail(Request $request)
    {
        try {
            $year = $request->input('year', date('Y'));
            $month = $request->input('month', date('n'));
            $type = $request->input('type', 'SO'); // SO, WO, LPO

            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $data = [];

            switch ($type) {
                case 'SO':
                    $data = DB::connection('mysql3')
                        ->table('salesorderh')
                        ->whereBetween('docdate', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->select('DocNo', 'docdate', 'status')
                        ->orderBy('docdate', 'desc')
                        ->get();
                    break;

                case 'WO':
                    // Ambil semua nomor SO untuk bulan ini
                    $soNumbers = DB::connection('mysql3')
                        ->table('salesorderh')
                        ->whereBetween('docdate', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->pluck('DocNo')
                        ->toArray();

                    if (!empty($soNumbers)) {
                        $data = DB::connection('mysql3')
                            ->table('workorderh')
                            ->whereIn('SODocNo', $soNumbers)
                            ->select('DocNo', 'DocDate', 'Status', 'SODocNo')
                            ->orderBy('DocDate', 'desc')
                            ->get();
                    }
                    break;

                case 'LPO':
                    // Ambil semua nomor SO untuk bulan ini
                    $soNumbers = DB::connection('mysql3')
                        ->table('salesorderh')
                        ->whereBetween('docdate', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->pluck('DocNo')
                        ->toArray();

                    if (!empty($soNumbers)) {
                        // Ambil semua nomor WO yang terkait dengan SO
                        $woNumbers = DB::connection('mysql3')
                            ->table('workorderh')
                            ->whereIn('SODocNo', $soNumbers)
                            ->pluck('DocNo')
                            ->toArray();

                        if (!empty($woNumbers)) {
                            $data = DB::connection('mysql4')
                                ->table('workorders_local')
                                ->whereIn('DocNo', $woNumbers)
                                ->select('DocNo', 'DocDate', 'Status')
                                ->orderBy('DocDate', 'desc')
                                ->get();

                            // Tambahkan informasi WO dan SO untuk setiap LPO
                            foreach ($data as $lpo) {
                                $wo = DB::connection('mysql3')
                                    ->table('workorderh')
                                    ->where('DocNo', $lpo->DocNo)
                                    ->first();

                                if ($wo) {
                                    $lpo->WODocNo = $wo->DocNo;
                                    $lpo->SODocNo = $wo->SODocNo;
                                }
                            }
                        }
                    }
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
