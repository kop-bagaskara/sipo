<?php

namespace App\Http\Controllers;

use App\Models\PelumasanMaintenance;
use App\Models\PlanContinuedProduction;
use App\Models\PlanFirstProduction;
use App\Models\RencanaPlan;
use App\Models\DatabaseMachine;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Machine;
use App\Models\MappingItem;
use App\Models\KodeMaterialSIM;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{
    public function pilihRencanaPlan()
    {
        return view('main.process.pilih-rencana-plan');
    }

    public function planningGlueing()
    {
        return view('main.process.planning-glueing');
    }

    public function indexRencanaPlan(Request $request)
    {

        if ($request->ajax()) {

            $data = RencanaPlan::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('r_plan', function ($machines) {
                    return $machines->r_plan;
                })
                ->addColumn('r_dept', function ($machines) {
                    return $machines->r_dept;
                })
                ->addColumn('updated_at', function ($machines) {
                    return $machines->updated_at;
                })
                ->addColumn('by', function ($machines) {
                    return $machines->by;
                })
                ->addColumn('action', function ($machines) {

                    $btn = '<a id="edit-data" data-id="' . $machines->id . '" class="btn btn-primary btn-sm edit-data text-light" data-original-title="Edit">Edit</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // return view('main.blade-system.master-rencana-plan');
    }

    public function indexDataWo(Request $request)
    {
        if ($request->ajax()) {
            $smSelected = $request->input('smSelected');
            $fromdate = $request->input('from');
            $todate = $request->input('to');
            $processes = $request->input('processes'); // Ambil parameter proses

            $SOWODocNo = [];
            $salesOrder = collect([]); // Initialize empty collection

            // Untuk PLONG, tidak perlu query salesOrder di awal (akan diambil nanti jika diperlukan)
            if ($processes !== 'PLONG') {
                $cacheKey = "salesOrders_{$fromdate}_{$todate}_{$smSelected}";
                $salesOrder = Cache::remember($cacheKey, 60, function () use ($fromdate, $todate) {
                    if ($fromdate && $todate) {
                        return DB::connection('mysql3')
                            ->table('salesorderh')
                            ->whereIn('series', ['SOD', 'SOP'])
                            ->whereIn('status', ['approved', 'incomplete'])
                            ->whereBetween('createdDate', [$fromdate, $todate])
                            ->select('DocNo', 'DeliveryDate')
                            ->orderBy('DeliveryDate', 'ASC')
                            ->get();
                    } else {
                        $threeMonthsAgo = now()->subMonths(3);
                        return DB::connection('mysql3')
                            ->table('salesorderh')
                            ->whereIn('series', ['SOD', 'SOP'])
                            ->whereIn('status', ['approved'])
                            ->whereDate('createdDate', '>=', $threeMonthsAgo)
                            ->select('DocNo', 'DeliveryDate')
                            ->orderBy('DeliveryDate', 'ASC')
                            ->get();
                    }
                });
            }

            // Ambil daftar WO, SO, kode item yang sudah pernah di-plan
            $planned = PlanFirstProduction::select('wo_docno', 'so_docno', 'code_item')->get();
            $plannedSet = $planned->map(function ($row) {
                return $row->wo_docno . '|' . $row->so_docno . '|' . $row->code_item;
            })->toArray();

            // Untuk PLONG: ambil item yang sudah finish dari proses sebelumnya (CETAK)
            $finishedItemsSet = [];
            $plannedForPlongSet = [];
            $plannedForPlongCount = []; // Hitung jumlah plan per item
            $finishedItemsData = []; // Untuk menyimpan data lengkap item yang finish
            if ($processes === 'PLONG') {
                // Query untuk ambil item yang sudah finish dari proses CETAK dengan filter tanggal finish
                $finishedItemsQuery = PlanFirstProduction::where('process', 'CTK');

                // Filter berdasarkan tanggal finish jika ada (updated_at adalah waktu finish)
                if ($fromdate && $todate) {
                    $finishedItemsQuery->whereBetween('updated_at', [$fromdate . ' 00:00:00', $todate . ' 23:59:59']);
                }

                $finishedItems = $finishedItemsQuery->select('wo_docno', 'so_docno', 'code_item', 'updated_at as finish_date')
                    ->get();

                $finishedItemsSet = $finishedItems->map(function ($row) {
                    return $row->wo_docno . '|' . $row->so_docno . '|' . $row->code_item;
                })->toArray();

                // Simpan data lengkap untuk mapping
                foreach ($finishedItems as $item) {
                    $key = $item->wo_docno . '|' . $item->so_docno . '|' . $item->code_item;
                    $finishedItemsData[$key] = $item;
                }

                // Juga cek dari tb_plan_continued_productions untuk item yang sudah finish
                $finishedContinuedQuery = DB::table('tb_plan_continued_productions')
                    ->where('process', 'CTK')
                    ->where('flag_status', 'FINISHED');

                // Filter berdasarkan tanggal finish jika ada (finish_time adalah waktu finish)
                if ($fromdate && $todate) {
                    $finishedContinuedQuery->whereBetween('finish_time', [$fromdate . ' 00:00:00', $todate . ' 23:59:59']);
                }

                $finishedContinued = $finishedContinuedQuery->select('wo_docno', 'so_docno', 'code_item', 'finish_time as finish_date')
                    ->get();

                $finishedContinuedSet = $finishedContinued->map(function ($row) {
                    return $row->wo_docno . '|' . $row->so_docno . '|' . $row->code_item;
                })->toArray();

                // Gabungkan kedua set
                $finishedItemsSet = array_unique(array_merge($finishedItemsSet, $finishedContinuedSet));

                // Simpan data lengkap dari continued productions juga
                foreach ($finishedContinued as $item) {
                    $key = $item->wo_docno . '|' . $item->so_docno . '|' . $item->code_item;
                    if (!isset($finishedItemsData[$key])) {
                        $finishedItemsData[$key] = $item;
                    }
                }

                // Ambil semua item yang sudah di-plan untuk PLONG dengan menghitung jumlahnya
                // Kita perlu menghitung berapa kali setiap item sudah di-plan
                $plannedForPlong = PlanFirstProduction::where('process', 'PLG')
                    ->select('wo_docno', 'so_docno', 'code_item')
                    ->get();

                // Hitung jumlah plan per item (bukan hanya cek ada/tidak)
                // Reset array untuk menghitung ulang
                $plannedForPlongCount = [];
                foreach ($plannedForPlong as $row) {
                    $key = $row->wo_docno . '|' . $row->so_docno . '|' . $row->code_item;
                    if (!isset($plannedForPlongCount[$key])) {
                        $plannedForPlongCount[$key] = 0;
                    }
                    $plannedForPlongCount[$key]++;
                }

                // Juga cek dari tb_plan_continued_productions
                $plannedContinued = DB::table('tb_plan_continued_productions')
                    ->whereIn('process', ['PLG', 'PLONG'])
                    ->select('wo_docno', 'so_docno', 'code_item')
                    ->get();

                foreach ($plannedContinued as $row) {
                    $key = $row->wo_docno . '|' . $row->so_docno . '|' . $row->code_item;
                    if (!isset($plannedForPlongCount[$key])) {
                        $plannedForPlongCount[$key] = 0;
                    }
                    $plannedForPlongCount[$key]++;
                }
            }

            // Untuk PLONG: ambil data langsung dari finishedItems tanpa query salesOrder
            if ($processes === 'PLONG' && !empty($finishedItemsSet)) {
                // Ambil semua WO DocNo dan MaterialCode dari finishedItems
                $woDocNos = [];
                $materialCodes = [];
                foreach ($finishedItemsData as $key => $item) {
                    $parts = explode('|', $key);
                    if (count($parts) >= 3) {
                        $woDocNos[] = $parts[0];
                        $materialCodes[] = $parts[2];
                    }
                }

                // Ambil data work order berdasarkan WO DocNo yang sudah finish
                if (!empty($woDocNos)) {
                    $workOrders = DB::connection('mysql3')
                        ->table('workorderh')
                        ->leftJoin('workorderd', 'workorderh.DocNo', 'workorderd.DocNo')
                        ->whereIn('workorderh.DocNo', array_unique($woDocNos))
                        ->whereNotIn('workorderh.status', ['DELETED'])
                        ->select('workorderh.DocNo', 'workorderh.MaterialCode', 'workorderh.Zupcetak', 'workorderh.Unit', 'workorderd.Qty', 'workorderh.SODocNo')
                        ->get();

                    // Ambil SO untuk delivery date
                    $soDocNos = $workOrders->pluck('SODocNo')->unique()->toArray();
                    $salesOrder = DB::connection('mysql3')
                        ->table('salesorderh')
                        ->whereIn('DocNo', $soDocNos)
                        ->select('DocNo', 'DeliveryDate')
                        ->get();

                    foreach ($workOrders as $workOrder) {
                        $key = $workOrder->DocNo . '|' . $workOrder->SODocNo . '|' . $workOrder->MaterialCode;

                        // Cek apakah item sudah finish dari proses sebelumnya
                        if (!in_array($key, $finishedItemsSet)) {
                            continue; // Skip item yang belum finish
                        }

                        // Hitung jumlah proses PLONG berdasarkan masterbomd terlebih dahulu
                        $jumlahPlong = 0;
                        $materialCodesPlong = [];
                        $materialCode = $workOrder->MaterialCode;

                        $bomDetails = DB::connection('mysql3')
                            ->table('masterbomd')
                            ->where('MaterialCode', 'LIKE', $materialCode . '%')
                            ->where('MaterialCode', '!=', $materialCode) // Exclude exact match
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '.WIP.PTG%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '.WIP.CTK%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.SU%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.KPS%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.STR%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.TUM%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.CT1%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.CT2%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.CT3%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.CT4%')
                            ->where('MaterialCode', 'NOT LIKE', $materialCode . '%WIP.CK1%')
                            ->select('MaterialCode')
                            ->distinct()
                            ->get();

                        $jumlahPlong = $bomDetails->count();
                        $materialCodesPlong = $bomDetails->pluck('MaterialCode')->toArray();

                        // Item hanya ditampilkan jika counter (jumlah PLONG) > 0
                        if ($jumlahPlong <= 0) {
                            continue; // Skip item yang tidak punya counter
                        }

                        // Cek berapa kali item sudah di-plan untuk PLONG
                        $sudahDiPlan = isset($plannedForPlongCount[$key]) ? $plannedForPlongCount[$key] : 0;

                        // Hitung jumlah PLONG yang tersisa (total - sudah di-plan)
                        $jumlahPlongTersisa = $jumlahPlong - $sudahDiPlan;

                        // Item hanya ditampilkan jika masih ada proses PLONG yang belum di-plan
                        // Jika sudah di-plan sebanyak jumlahPlong, skip item ini
                        if ($sudahDiPlan >= $jumlahPlong) {
                            continue; // Skip item yang sudah di-plan semua proses PLONG-nya
                        }

                        $status = 'OPEN';
                        $exists = collect($SOWODocNo)->contains(function ($item) use ($workOrder) {
                            return $item['WODocNo'] === $workOrder->DocNo && $item['MaterialCode'] === $workOrder->MaterialCode;
                        });

                        if (!$exists) {
                            $name_material = DB::connection('mysql3')->table('mastermaterial')->where('Code', $workOrder->MaterialCode)->first();
                            $soData = $salesOrder->firstWhere('DocNo', $workOrder->SODocNo);

                            $SOWODocNo[] = [
                                'SODocNo' => $workOrder->SODocNo,
                                'WODocNo' => $workOrder->DocNo,
                                'MaterialCode' => $workOrder->MaterialCode,
                                'Name' => $name_material ? $name_material->Name : 'Unknown',
                                'Unit' => $workOrder->Unit,
                                'Quantity' => $workOrder->Qty,
                                'Up' => $workOrder->Zupcetak,
                                'DeliveryDate' => $soData ? $soData->DeliveryDate : now()->format('Y-m-d'),
                                'Status' => $status,
                                'JumlahPlong' => $jumlahPlongTersisa, // Jumlah PLONG yang tersisa (total - sudah di-plan)
                                'MaterialCodesPlong' => $materialCodesPlong, // Daftar material code dari masterbomd
                            ];
                        }
                    }
                }
            } elseif ($salesOrder->isNotEmpty()) {
                // Untuk proses selain PLONG, gunakan logika lama
                $salesOrderDocNos = $salesOrder->pluck('DocNo')->toArray();

                $workOrders = DB::connection('mysql3')
                    ->table('workorderh')
                    ->leftJoin('workorderd', 'workorderh.DocNo', 'workorderd.DocNo')
                    ->whereIn('SODocNo', $salesOrderDocNos)
                    ->whereNotIn('status', ['DELETED'])
                    ->select('workorderh.DocNo', 'workorderh.MaterialCode', 'workorderh.Zupcetak', 'workorderh.Unit', 'workorderd.Qty', 'workorderh.SODocNo')
                    ->get();

                if ($workOrders->isNotEmpty()) {
                    $workOrderDocNos = $workOrders->pluck('DocNo')->toArray();
                    $unfinishedJobOrders = $workOrderDocNos;
                    $targetProduction = '';

                    foreach ($workOrders as $workOrder) {
                        $key = $workOrder->DocNo . '|' . $workOrder->SODocNo . '|' . $workOrder->MaterialCode;

                        if (in_array($workOrder->DocNo, $unfinishedJobOrders) && !in_array($key, $plannedSet)) {
                            $status = 'OPEN';
                            if (isset($targetProduction[$workOrder->SODocNo])) {
                                $status = $targetProduction[$workOrder->SODocNo] === 'PLAN' ? 'DALAM PROSES' : 'OPEN';
                            }
                            $exists = collect($SOWODocNo)->contains(function ($item) use ($workOrder) {
                                return $item['WODocNo'] === $workOrder->DocNo;
                            });
                            if (!$exists) {
                                $name_material = DB::connection('mysql3')->table('mastermaterial')->where('Code', $workOrder->MaterialCode)->first();
                                $SOWODocNo[] = [
                                    'SODocNo' => $workOrder->SODocNo,
                                    'WODocNo' => $workOrder->DocNo,
                                    'MaterialCode' => $workOrder->MaterialCode,
                                    'Name' => $name_material->Name,
                                    'Unit' => $workOrder->Unit,
                                    'Quantity' => $workOrder->Qty,
                                    'Up' => $workOrder->Zupcetak,
                                    'DeliveryDate' => $salesOrder->firstWhere('DocNo', $workOrder->SODocNo)->DeliveryDate,
                                    'Status' => $status,
                                ];
                            }
                        }
                    }
                }
            }

            usort($SOWODocNo, function ($a, $b) {
                return strtotime($a['DeliveryDate']) <=> strtotime($b['DeliveryDate']);
            });

            $dataTable = DataTables::of($SOWODocNo)
                ->addIndexColumn();

            // Tambahkan kolom JumlahPlong hanya jika proses = PLONG
            if ($processes === 'PLONG') {
                $dataTable->addColumn('JumlahPlong', function ($row) {
                    $jumlah = $row['JumlahPlong'] ?? 0;
                    $materialCodes = $row['MaterialCodesPlong'] ?? [];

                    // Buat tooltip dengan daftar material code (format lebih readable)
                    $tooltipContent = '';
                    if (!empty($materialCodes)) {
                        $tooltipList = '<div style="text-align: left;"><strong>Material Codes:</strong><br>';
                        foreach ($materialCodes as $code) {
                            $tooltipList .= '• ' . htmlspecialchars($code) . '<br>';
                        }
                        $tooltipList .= '</div>';
                        $tooltipContent = 'title="' . htmlspecialchars($tooltipList) . '" data-toggle="tooltip" data-placement="top" data-html="true"';
                    }

                    return '<span class="badge badge-warning" ' . $tooltipContent . ' style="cursor: help;">' . $jumlah . '</span>';
                })
                    // Tambahkan MaterialCodesPlong sebagai kolom tersembunyi agar bisa diakses di frontend
                    ->addColumn('MaterialCodesPlong', function ($row) {
                        // Return sebagai array (DataTables akan otomatis serialize ke JSON)
                        return $row['MaterialCodesPlong'] ?? [];
                    });
            }

            return $dataTable
                ->addColumn('SODocNo', function ($row) {
                    return $row['SODocNo'];
                })
                ->addColumn('WODocNo', function ($row) {
                    return $row['WODocNo'];
                })
                ->addColumn('Up', function ($row) {
                    return $row['Up'];
                })
                ->addColumn('Unit', function ($row) {
                    return $row['Unit'];
                })
                ->addColumn('Quantity', function ($row) {
                    return $row['Quantity'];
                })
                ->addColumn('MaterialName', function ($row) {
                    return $row['Name'];
                })
                ->addColumn('MaterialCode', function ($row) {
                    return $row['MaterialCode'];
                })
                ->addColumn('DeliveryDate', function ($row) {
                    return $row['DeliveryDate'];
                })
                ->addColumn('Detail', function ($row) {
                    return '<button type="button" class="btn btn-info btn-detail" data-sodocno="' . $row['SODocNo'] . '">Detail</button>';
                })
                ->addColumn('Status', function ($row) {
                    return $row['Status'];
                })
                ->rawColumns($processes === 'PLONG' ? ['Detail', 'JumlahPlong'] : ['Detail'])
                ->make(true);
        }
    }

    public function indexDataHasilCetak(Request $request)
    {
        if ($request->ajax()) {
            $data = PlanContinuedProduction::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function submitPlanFirst(Request $request)
    {
        // dd($request->all());
        // Log request data untuk debugging
        Log::info('submitPlanFirst called with data:', [
            'request_data' => $request->all(),
            'data_count' => $request->data ? count($request->data) : 0,
            'start_date' => $request->start_date,
            'processes' => $request->processes
        ]);

        $data = $request->data;
        $startDate = $request->start_date;
        $selectedProcess = $request->processes; // Proses yang dipilih user (CETAK, PTG, dll)
        $department = $request->input('department'); // Ambil department dari request jika ada

        // Validasi data yang diterima
        if (!$data || !is_array($data) || empty($data)) {
            Log::error('Invalid data received in submitPlanFirst');
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid atau kosong'
            ], 400);
        }

        if (!$startDate) {
            Log::error('Start date is required');
            return response()->json([
                'success' => false,
                'message' => 'Start date diperlukan'
            ], 400);
        }

        if (!$selectedProcess) {
            Log::error('Process selection is required');
            return response()->json([
                'success' => false,
                'message' => 'Pemilihan proses diperlukan'
            ], 400);
        }

        // Generate code plan
        $prefix = 'P-PPIC';
        $currentDate = Carbon::now()->format('Ymd');
        $lastPlan = PlanFirstProduction::whereDate('created_at', now()->toDateString())
            ->where('code_plan', 'like', $prefix . '-' . $currentDate . '-%')
            ->orderBy('code_plan', 'desc')
            ->first();

        $codeNumber = 1;
        if ($lastPlan) {
            $lastCode = $lastPlan->code_plan;
            $lastNum = intval(substr($lastCode, strrpos($lastCode, '-') + 1));
            $codeNumber = $lastNum + 1;
        }
        $codeNumberPadded = str_pad($codeNumber, 3, '0', STR_PAD_LEFT);
        $code_plan = $prefix . '-' . $currentDate . '-' . $codeNumberPadded;

        Log::info('Generated code plan:', ['code_plan' => $code_plan]);

        // Proses data untuk preview
        $planPerItem = [];
        $startTime = Carbon::parse($startDate)->setTime(8, 0, 0); // Mulai jam 8 pagi

        // Ambil semua mesin yang akan digunakan untuk mendapatkan end_jam terakhir
        $machinesToCheck = [];
        foreach ($data as $item) {
            if (!isset($item['MaterialCode']) || !isset($item['WODocNo']) || !isset($item['Quantity'])) {
                continue;
            }
            // Ambil mesin untuk item ini (akan digunakan nanti)
            $machine = $this->getDefaultMachineForProcess($selectedProcess, $item['MaterialCode'], $department);
            if ($machine) {
                $machinesToCheck[$machine] = true;
            }
        }
        $machinesToCheck = array_keys($machinesToCheck);

        // Ambil end_jam terakhir dari database untuk setiap mesin
        // Filter berdasarkan process jika proses PLONG
        $machineLastEnd = [];
        if (!empty($machinesToCheck)) {
            // Ambil dari tb_plan_first_productions
            $query = PlanFirstProduction::whereIn('code_machine', $machinesToCheck)
                ->whereNotNull('end_jam')
                ->where('flag_status', '!=', 'FINISH');

            // Untuk PLONG, filter juga berdasarkan process PLG
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                $query->whereIn('process', ['PLG', 'PLONG']);
            }

            $existingPlans = $query->orderBy('end_jam', 'desc')
                ->get(['code_machine', 'end_jam']);

            foreach ($existingPlans as $plan) {
                if (!isset($machineLastEnd[$plan->code_machine])) {
                    $machineLastEnd[$plan->code_machine] = Carbon::parse($plan->end_jam);
                }
            }

            // Untuk PLONG, juga cek dari tb_plan_continued_productions
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                $continuedPlans = DB::table('tb_plan_continued_productions')
                    ->whereIn('code_machine', $machinesToCheck)
                    ->whereIn('process', ['PLG', 'PLONG'])
                    ->whereNotNull('end_jam')
                    ->where('flag_status', '!=', 'FINISHED')
                    ->orderBy('end_jam', 'desc')
                    ->get(['code_machine', 'end_jam']);

                foreach ($continuedPlans as $plan) {
                    $planEndTime = Carbon::parse($plan->end_jam);
                    if (!isset($machineLastEnd[$plan->code_machine])) {
                        $machineLastEnd[$plan->code_machine] = $planEndTime;
                    } else {
                        // Ambil yang lebih besar (lebih akhir)
                        if ($planEndTime->gt($machineLastEnd[$plan->code_machine])) {
                            $machineLastEnd[$plan->code_machine] = $planEndTime;
                        }
                    }
                }
            }
        }

        // Track start time per mesin untuk item berikutnya di mesin yang sama
        $machineStartTimes = [];

        foreach ($data as $itemIndex => $item) {

            // dd($item);
            // Validasi item data
            if (!isset($item['MaterialCode']) || !isset($item['WODocNo']) || !isset($item['Quantity'])) {
                Log::warning('Invalid item data:', $item);
                continue;
            }

            // Log semua data item untuk debugging PLONG
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                Log::info("PLONG Item Data Received - Index: {$itemIndex}", [
                    'material_code' => $item['MaterialCode'] ?? 'N/A',
                    'wo_docno' => $item['WODocNo'] ?? 'N/A',
                    'code_item_bom' => $item['CodeItemBOM'] ?? 'N/A',
                    'material_code_bom' => $item['MaterialCodeBOM'] ?? 'N/A',
                    'plong_index' => $item['plongIndex'] ?? 'N/A',
                    'all_keys' => array_keys($item)
                ]);
            }

            // Buat unique key untuk item
            // Untuk PLONG: tambahkan CodeItemBOM atau plongIndex ke unique key agar duplikat tidak saling menimpa
            // Prioritas: CodeItemBOM > plongIndex > default
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                // Cek CodeItemBOM dulu (prioritas tertinggi)
                if (isset($item['CodeItemBOM']) && $item['CodeItemBOM'] !== null && $item['CodeItemBOM'] !== '') {
                    $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'] . '_' . $item['CodeItemBOM'];
                    Log::info("PLONG item with CodeItemBOM - UniqueKey: {$uniqueKey}, CodeItemBOM: {$item['CodeItemBOM']}", [
                        'item_index' => $itemIndex,
                        'material_code' => $item['MaterialCode'],
                        'wo_docno' => $item['WODocNo'],
                        'code_item_bom' => $item['CodeItemBOM']
                    ]);
                }
                // Jika tidak ada CodeItemBOM, cek MaterialCodeBOM (alias)
                elseif (isset($item['MaterialCodeBOM']) && $item['MaterialCodeBOM'] !== null && $item['MaterialCodeBOM'] !== '') {
                    $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'] . '_' . $item['MaterialCodeBOM'];
                    Log::info("PLONG item with MaterialCodeBOM - UniqueKey: {$uniqueKey}, MaterialCodeBOM: {$item['MaterialCodeBOM']}", [
                        'item_index' => $itemIndex,
                        'material_code' => $item['MaterialCode'],
                        'wo_docno' => $item['WODocNo'],
                        'material_code_bom' => $item['MaterialCodeBOM']
                    ]);
                }
                // Jika tidak ada CodeItemBOM, gunakan plongIndex
                elseif (isset($item['plongIndex']) && $item['plongIndex'] !== null) {
                    $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'] . '_PLONG' . $item['plongIndex'];
                    Log::info("PLONG item with plongIndex - UniqueKey: {$uniqueKey}, plongIndex: {$item['plongIndex']}", [
                        'item_index' => $itemIndex,
                        'material_code' => $item['MaterialCode'],
                        'wo_docno' => $item['WODocNo'],
                        'plong_index' => $item['plongIndex']
                    ]);
                }
                // Fallback: gunakan itemIndex untuk memastikan unik
                else {
                    $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'] . '_PLONG_' . $itemIndex;
                    Log::warning("PLONG item without CodeItemBOM, MaterialCodeBOM, or plongIndex - Using itemIndex for uniqueKey: {$uniqueKey}", [
                        'item_index' => $itemIndex,
                        'item_data' => $item
                    ]);
                }
            } else {
                $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'];
            }

            // Ambil mesin untuk item ini
            // Untuk PLONG: machine harus null (tidak ambil dari mapping), gunakan 'PLONG_GLOBAL' sebagai key untuk tracking waktu
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                $machine = null; // Untuk PLONG, machine harus null
                $machineKey = 'PLONG_GLOBAL'; // Gunakan key global untuk tracking waktu
            } else {
                $machine = $this->getDefaultMachineForProcess($selectedProcess, $item['MaterialCode'], $department);
                $machineKey = $machine;
            }

            // dd($machine);

            // Tentukan start time untuk item ini
            // Untuk PLONG: gunakan global time tracking (tidak per mesin karena code_machine = null)
            // Untuk proses lain: gunakan per mesin seperti biasa
            if (isset($machineLastEnd[$machineKey])) {
                $itemStartTime = $machineLastEnd[$machineKey];
                Log::info("Machine key {$machineKey} has existing plans, starting from last end_jam: " . $itemStartTime->format('Y-m-d H:i:s'));
            } elseif (isset($machineStartTimes[$machineKey])) {
                // Jika sudah punya start time dari item sebelumnya, gunakan itu
                $itemStartTime = $machineStartTimes[$machineKey];
            } else {
                // Item baru, mulai dari startTime default (jam 8)
                $itemStartTime = $startTime;
            }

            // Generate planning data untuk proses yang dipilih
            $planData = $this->generateSimpleProcessPlan($item, $selectedProcess, $itemStartTime);
            // dd($planData);

            if ($planData) {
                // Pastikan CodeItemBOM tersimpan di planData untuk PLONG
                if ((strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') && isset($item['CodeItemBOM'])) {
                    $planData['CodeItemBOM'] = $item['CodeItemBOM'];
                    $planData['MaterialCodeBOM'] = $item['CodeItemBOM'];
                    Log::info("CodeItemBOM added to planData for PLONG", [
                        'unique_key' => $uniqueKey,
                        'code_item_bom' => $item['CodeItemBOM']
                    ]);
                } elseif ((strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') && isset($item['plongIndex'])) {
                    // Jika tidak ada CodeItemBOM, gunakan plongIndex sebagai fallback
                    $planData['plongIndex'] = $item['plongIndex'];
                    Log::info("plongIndex added to planData for PLONG", [
                        'unique_key' => $uniqueKey,
                        'plong_index' => $item['plongIndex']
                    ]);
                }

                // Log sebelum menyimpan ke planPerItem
                if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                    Log::info("Saving PLONG item to planPerItem", [
                        'unique_key' => $uniqueKey,
                        'material_code' => $planData['MaterialCode'] ?? 'N/A',
                        'wo_docno' => $planData['WODocNo'] ?? 'N/A',
                        'code_item_bom' => $planData['CodeItemBOM'] ?? 'N/A',
                        'plong_index' => $planData['plongIndex'] ?? 'N/A',
                        'total_items_before' => count($planPerItem)
                    ]);
                }

                $planPerItem[$uniqueKey] = $planData;

                // Log setelah menyimpan
                if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                    Log::info("PLONG item saved to planPerItem", [
                        'unique_key' => $uniqueKey,
                        'total_items_after' => count($planPerItem)
                    ]);
                }
                // Update start time untuk item berikutnya
                // Untuk PLONG: gunakan machineKey (PLONG_GLOBAL) untuk tracking waktu global
                // Untuk proses lain: gunakan machine seperti biasa
                $machineStartTimes[$machineKey] = Carbon::parse($planData['EndJam'])->addHour();
                // Update juga machineLastEnd untuk tracking
                if (!isset($machineLastEnd[$machineKey])) {
                    $machineLastEnd[$machineKey] = Carbon::parse($planData['EndJam']);
                } else {
                    // Update jika end_jam baru lebih besar
                    $newEnd = Carbon::parse($planData['EndJam']);
                    if ($newEnd->gt($machineLastEnd[$machineKey])) {
                        $machineLastEnd[$machineKey] = $newEnd;
                    }
                }
            }
        }

        Log::info('Generated plan data:', [
            'total_items' => count($planPerItem),
            'selected_process' => $selectedProcess,
            'start_date' => $startDate
        ]);

        // dd($planPerItem);

        // Cek apakah request untuk save ke database
        $saveToDatabase = $request->input('save_to_database', false);

        if ($saveToDatabase) {
            // Ambil data PPOS dari request dan tambahkan ke planPerItem
            $pposSchedules = $request->input('ppos_schedules', []);
            Log::info('PPOS Schedules received:', ['ppos_schedules' => $pposSchedules]);

            // Tambahkan data PPOS ke dalam planPerItem sebagai item terpisah
            if (!empty($pposSchedules)) {
                foreach ($pposSchedules as $machineCode => $pposDataArray) {
                    // Handle both single object and array of objects
                    $pposDataList = is_array($pposDataArray) && isset($pposDataArray[0]) ? $pposDataArray : [$pposDataArray];

                    foreach ($pposDataList as $index => $pposData) {
                        $pposKey = 'PPOS_' . $machineCode . '_' . ($index + 1);

                        // Hitung durasi PPOS
                        $startDateTime = Carbon::parse($pposData['start_date'] . ' ' . $pposData['start_time']);
                        $endDateTime = Carbon::parse($pposData['end_date'] . ' ' . $pposData['end_time']);
                        $duration = $endDateTime->diffInHours($startDateTime);

                        $planPerItem[$pposKey] = [
                            'MaterialCode' => 'PPOS-' . $machineCode . '-' . ($index + 1),
                            'MaterialName' => 'JADWAL PPOS - ' . ($pposData['item'] ?? 'ITEM PPOS'),
                            'WODocNo' => 'PPOS-' . $machineCode . '-' . ($index + 1),
                            'SODocNo' => 'PPOS',
                            'Quantity' => 0,
                            'UP' => 1,
                            'Unit' => 'PCS',
                            'MachineUnit' => 'PCS', // Tambahkan MachineUnit untuk PPOS
                            'Machine' => $machineCode,
                            'MachineCapacity' => 0,
                            'Estimation' => $duration,
                            'StartJam' => $startDateTime->format('Y-m-d H:i:s'),
                            'EndJam' => $endDateTime->format('Y-m-d H:i:s'),
                            'DeliveryDate' => $pposData['start_date'],
                            'Proses' => 'PPOS', // Tambahkan Proses untuk konsistensi
                            'Process' => 'PPOS',
                            'Department' => 'MAINTENANCE',
                            'isPPOS' => true,
                            'notes' => $pposData['notes'] ?? '',
                            'order' => 999, // Order tinggi agar PPOS muncul di akhir
                            'ConvertedQuantity' => 0,
                            'ConversionApplied' => false
                        ];
                    }
                }
            }

            // Simpan ke database
            try {
                $savedData = $this->savePlanningToDatabase($planPerItem, $selectedProcess, $startDate, $code_plan);


                return response()->json([
                    'success' => true,
                    'message' => 'Planning berhasil disimpan ke database',
                    'data' => $savedData
                ]);
            } catch (Exception $e) {
                Log::error('Error saving planning to database: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan planning ke database: ' . $e->getMessage()
                ], 500);
            }
        } else {

            // dd($planPerItem);
            // Return preview data
            if (!empty($planPerItem)) {
                return response()->json([
                    'success' => false,
                    'preview' => true,
                    'message' => 'Preview rencana produksi untuk proses ' . $selectedProcess . ' berhasil dibuat',
                    'data' => [
                        'planPerItem' => $planPerItem,
                        'selectedProcess' => $selectedProcess,
                        'startDate' => $startDate,
                        'code_plan' => $code_plan
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'preview' => false,
                    'message' => 'Gagal membuat preview rencana produksi'
                ]);
            }
        }
    }

    /**
     * Submit plan GLUEING - khusus untuk proses GLUEING menggunakan m_lem
     */
    public function submitPlanGlueing(Request $request)
    {
        Log::info('submitPlanGlueing called with data:', [
            'request_data' => $request->all(),
            'data_count' => $request->data ? count($request->data) : 0,
            'start_date' => $request->start_date,
        ]);

        $data = $request->data;
        $startDate = $request->start_date;
        $selectedProcess = 'GLUEING'; // Hardcode untuk GLUEING
        $department = $request->input('department');

        // Validasi data yang diterima
        if (!$data || !is_array($data) || empty($data)) {
            Log::error('Invalid data received in submitPlanGlueing');
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid atau kosong'
            ], 400);
        }

        if (!$startDate) {
            Log::error('Start date is required');
            return response()->json([
                'success' => false,
                'message' => 'Start date diperlukan'
            ], 400);
        }

        // Generate code plan
        $prefix = 'P-PPIC';
        $currentDate = Carbon::now()->format('Ymd');
        $lastPlan = PlanFirstProduction::whereDate('created_at', now()->toDateString())
            ->where('code_plan', 'like', $prefix . '-' . $currentDate . '-%')
            ->orderBy('code_plan', 'desc')
            ->first();

        $codeNumber = 1;
        if ($lastPlan) {
            $lastCode = $lastPlan->code_plan;
            $lastNum = intval(substr($lastCode, strrpos($lastCode, '-') + 1));
            $codeNumber = $lastNum + 1;
        }
        $codeNumberPadded = str_pad($codeNumber, 3, '0', STR_PAD_LEFT);
        $code_plan = $prefix . '-' . $currentDate . '-' . $codeNumberPadded;

        Log::info('Generated code plan for GLUEING:', ['code_plan' => $code_plan]);

        // Proses data untuk preview
        $planPerItem = [];
        $startTime = Carbon::parse($startDate)->setTime(8, 0, 0); // Mulai jam 8 pagi

        // Ambil semua mesin yang akan digunakan untuk mendapatkan end_jam terakhir
        $machinesToCheck = [];
        foreach ($data as $item) {
            if (!isset($item['MaterialCode']) || !isset($item['WODocNo']) || !isset($item['Quantity'])) {
                continue;
            }
            // Ambil mesin untuk item ini menggunakan m_lem
            $machine = $this->getMachineForGlueing($item['MaterialCode']);
            if ($machine) {
                $machinesToCheck[$machine] = true;
            }
        }
        $machinesToCheck = array_keys($machinesToCheck);

        // Ambil end_jam terakhir dari database untuk setiap mesin
        $machineLastEnd = [];
        if (!empty($machinesToCheck)) {
            $existingPlans = PlanFirstProduction::whereIn('code_machine', $machinesToCheck)
                ->where('process', 'GLUEING')
                ->whereNotNull('end_jam')
                ->where('flag_status', '!=', 'FINISH')
                ->orderBy('end_jam', 'desc')
                ->get(['code_machine', 'end_jam']);

            foreach ($existingPlans as $plan) {
                if (!isset($machineLastEnd[$plan->code_machine])) {
                    $machineLastEnd[$plan->code_machine] = Carbon::parse($plan->end_jam);
                }
            }
        }

        // Track start time per mesin untuk item berikutnya di mesin yang sama
        $machineStartTimes = [];

        foreach ($data as $itemIndex => $item) {
            // Validasi item data
            if (!isset($item['MaterialCode']) || !isset($item['WODocNo']) || !isset($item['Quantity'])) {
                Log::warning('Invalid item data in submitPlanGlueing:', $item);
                continue;
            }

            // Buat unique key untuk item
            $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'];

            // Ambil mesin untuk item ini menggunakan m_lem
            $machine = $this->getMachineForGlueing($item['MaterialCode']);
            $machineKey = $machine;

            // Tentukan start time untuk item ini
            if (isset($machineLastEnd[$machineKey])) {
                $itemStartTime = $machineLastEnd[$machineKey];
                Log::info("Machine key {$machineKey} has existing plans, starting from last end_jam: " . $itemStartTime->format('Y-m-d H:i:s'));
            } elseif (isset($machineStartTimes[$machineKey])) {
                $itemStartTime = $machineStartTimes[$machineKey];
            } else {
                $itemStartTime = $startTime;
            }

            // Generate planning data untuk GLUEING
            $planData = $this->generateGlueingProcessPlan($item, $itemStartTime);

            if ($planData) {
                $planPerItem[$uniqueKey] = $planData;

                // Update start time untuk item berikutnya
                $machineStartTimes[$machineKey] = Carbon::parse($planData['EndJam'])->addHour();
                if (!isset($machineLastEnd[$machineKey])) {
                    $machineLastEnd[$machineKey] = Carbon::parse($planData['EndJam']);
                } else {
                    $newEnd = Carbon::parse($planData['EndJam']);
                    if ($newEnd->gt($machineLastEnd[$machineKey])) {
                        $machineLastEnd[$machineKey] = $newEnd;
                    }
                }
            }
        }

        Log::info('Generated GLUEING plan data:', [
            'total_items' => count($planPerItem),
            'start_date' => $startDate
        ]);

        // Cek apakah request untuk save ke database
        $saveToDatabase = $request->input('save_to_database', false);

        if ($saveToDatabase) {
            // Simpan ke database
            try {
                $savedData = $this->savePlanningToDatabase($planPerItem, $selectedProcess, $startDate, $code_plan);

                return response()->json([
                    'success' => true,
                    'message' => 'Planning GLUEING berhasil disimpan ke database',
                    'data' => $savedData
                ]);
            } catch (Exception $e) {
                Log::error('Error saving GLUEING planning to database: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan planning GLUEING ke database: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Return preview data
            if (!empty($planPerItem)) {
                return response()->json([
                    'success' => false,
                    'preview' => true,
                    'message' => 'Preview rencana produksi GLUEING berhasil dibuat',
                    'data' => [
                        'planPerItem' => $planPerItem,
                        'selectedProcess' => $selectedProcess,
                        'startDate' => $startDate,
                        'code_plan' => $code_plan
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'preview' => false,
                    'message' => 'Gagal membuat preview rencana produksi GLUEING'
                ]);
            }
        }
    }

    /**
     * Get machine untuk GLUEING dari m_lem
     */
    private function getMachineForGlueing($materialCode)
    {
        try {
            // Bersihkan material code dari suffix seperti .WIP.CTK, .WIP.PTG, dll
            $cleanMaterialCode = $materialCode;
            if (strpos($materialCode, '.WIP.') !== false) {
                $cleanMaterialCode = substr($materialCode, 0, strpos($materialCode, '.WIP.'));
            }

            // Coba cari dengan material code asli dulu
            $mapping = DB::table('tb_mapping_items')
                ->where('kode', $materialCode)
                ->whereNotNull('m_lem')
                ->where('m_lem', '!=', '')
                ->first();

            // Jika tidak ditemukan, coba dengan clean material code
            if (!$mapping && $cleanMaterialCode !== $materialCode) {
                $mapping = DB::table('tb_mapping_items')
                    ->where('kode', $cleanMaterialCode)
                    ->whereNotNull('m_lem')
                    ->where('m_lem', '!=', '')
                    ->first();
            }

            if ($mapping) {
                Log::info("Machine mapping found for GLUEING (m_lem)", [
                    'materialCode' => $materialCode,
                    'cleanMaterialCode' => $cleanMaterialCode,
                    'machine' => $mapping->m_lem
                ]);
                return $mapping->m_lem;
            }

            Log::warning("No machine mapping (m_lem) found for GLUEING process", [
                'materialCode' => $materialCode,
                'cleanMaterialCode' => $cleanMaterialCode
            ]);
        } catch (Exception $e) {
            Log::error("Error getting machine mapping for GLUEING: " . $e->getMessage());
        }

        // Fallback ke default machine
        return 'TIDAK TERSEDIA';
    }

    /**
     * Generate GLUEING process plan
     */
    private function generateGlueingProcessPlan($item, $startTime)
    {
        try {
            $materialCode = $item['MaterialCode'] ?? '';
            $materialName = $item['MaterialName'] ?? $item['Name'] ?? 'Unknown Material';

            Log::info("Generating GLUEING plan for material", [
                'materialCode' => $materialCode,
                'materialName' => $materialName
            ]);

            // Ambil mesin dari m_lem
            $machine = $this->getMachineForGlueing($materialCode);

            Log::info("Machine retrieved for GLUEING", [
                'materialCode' => $materialCode,
                'machine' => $machine
            ]);

            $machineDetail = Machine::where('Code', $machine)->first();

            $unitMachine = $machineDetail ? $machineDetail->Unit : 'PCS';
            $woUnit = $item['Unit'] ?? 'PCS';
            $woUp = intval($item['UP'] ?? 1);
            $quantity = intval($item['Quantity'] ?? 1);
            $woDocNo = $item['WODocNo'] ?? '';
            $soDocNo = $item['SODocNo'] ?? '';

            // Cari kapasitas dan unit mesin
            $machineData = Machine::where('Code', $machine)->first();
            $machineCapacity = $machineData ? floatval($machineData->CapacityPerHour) : 1000;
            $machineUnit = $machineData ? $machineData->Unit : 'PCS';

            // Konversi quantity jika unit berbeda
            $convertedQuantity = $quantity;
            $conversionApplied = false;

            if ($woUnit !== $machineUnit && $woUp > 1) {
                $convertedQuantity = $quantity / $woUp;
                $conversionApplied = true;
                Log::info("Unit conversion applied for GLUEING {$materialCode}: {$quantity} {$woUnit} / {$woUp} = {$convertedQuantity} {$machineUnit}");
            }

            // Hitung estimasi waktu
            $estimation = 0;
            if ($machineCapacity > 0) {
                $estimation = $convertedQuantity / $machineCapacity;
            } else {
                $estimation = 1; // Fallback minimal 1 jam
                Log::warning("Machine capacity is 0 for GLUEING {$materialCode}, using fallback estimation: {$estimation} hours");
            }

            // Validasi estimasi
            if ($estimation <= 0 || is_nan($estimation) || is_infinite($estimation)) {
                $estimation = 1; // Fallback minimal 1 jam
                Log::warning("Invalid estimation calculated for GLUEING {$materialCode}, using fallback: {$estimation} hours");
            }

            Log::info("GLUEING time calculation for {$materialCode}: {$convertedQuantity} {$machineUnit} / {$machineCapacity} = {$estimation} hours");

            // Hitung start dan end time
            $startTime = clone $startTime;
            $endTime = (clone $startTime)->addHours($estimation);

            return [
                'MaterialCode' => $materialCode,
                'MaterialName' => $materialName,
                'Quantity' => $quantity,
                'WODocNo' => $woDocNo,
                'SODocNo' => $soDocNo,
                'Proses' => 'GLUEING',
                'Machine' => $machine,
                'Estimation' => $estimation,
                'StartJam' => $startTime->format('Y-m-d H:i:s'),
                'EndJam' => $endTime->format('Y-m-d H:i:s'),
                'DeliveryDate' => $item['DeliveryDate'] ?? $startTime->format('Y-m-d'),
                'order' => $item['order'] ?? 1,
                'Unit' => $woUnit,
                'UP' => $woUp,
                'MachineUnit' => $machineUnit,
                'MachineCapacity' => $machineCapacity,
                'ConvertedQuantity' => $convertedQuantity,
                'ConversionApplied' => $conversionApplied,
                'OriginalQuantity' => $quantity,
            ];
        } catch (Exception $e) {
            Log::error('Error generating GLUEING process plan:', [
                'item' => $item,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Simpan planning ke database tb_plan_first_production
     */
    private function savePlanningToDatabase($planPerItem, $selectedProcess, $startDate, $code_plan)
    {
        // DD UNTUK DEBUGGING - HAPUS SETELAH FIX
        // dd([
        //     'DEBUG DATA SEBELUM INSERT',
        //     'planPerItem' => $planPerItem,
        //     'selectedProcess' => $selectedProcess,
        //     'startDate' => $startDate,
        //     'code_plan' => $code_plan,
        //     'planPerItem_count' => count($planPerItem),
        //     'sample_item' => $planPerItem ? array_values($planPerItem)[0] : 'No items'
        // ]);

        try {
            DB::beginTransaction();

            $savedItems = [];
            $currentStartTime = Carbon::parse($startDate)->setTime(8, 0, 0); // Mulai jam 8 pagi

            // HITUNG ULANG TIMELINE AGAR SESUAI DENGAN VIEW
            // dd($planPerItem);
            $recalculatedItems = $this->recalculateTimelineForView($planPerItem, $currentStartTime, $selectedProcess);

            // Proses yang dipilih
            if ($selectedProcess == 'CETAK') {
                $process = 'CTK';
            } elseif ($selectedProcess == 'POTONG') {
                $process = 'PTG';
            } elseif ($selectedProcess == 'PLONG') {
                $process = 'PLG';
            } elseif ($selectedProcess == 'EMBOSS') {
                $process = 'EMB';
            } elseif ($selectedProcess == 'SORTIR') {
                $process = 'STR';
            } elseif ($selectedProcess == 'GLUEING') {
                $process = 'LEM';
            }

            // Department

            // dd($recalculatedItems);

            foreach ($recalculatedItems as $uniqueKey => $planData) {
                // Cek apakah ini data PPOS
                $isPPOS = isset($planData['isPPOS']) && $planData['isPPOS'] === true;

                if ($isPPOS) {
                    // Untuk data PPOS, gunakan department MAINTENANCE
                    $department = 'MAINTENANCE';
                    $processType = 'PPOS';
                } else {
                    // Untuk data normal, ambil dari database
                    $dept = Machine::where('Code', $planData['Machine'])->first();
                    $department = $dept->Department ?? '';
                    $processType = $process;
                }

                // Simpan ke tabel tb_plan_first_production sesuai struktur yang ada
                // Untuk PLONG: code_machine dibiarkan kosong (null)
                // Untuk CETAK dan proses lain: ambil dari Machine (yang sudah diambil dari m_ctk)
                $codeMachine = (strtoupper($process) === 'PLONG' || strtoupper($process) === 'PLG') ? null : $planData['Machine'];

                // Untuk PLONG: ambil code_item_bom dari data yang dikirim
                $codeItemBOM = (strtoupper($process) === 'PLONG' || strtoupper($process) === 'PLG')
                    ? ($planData['CodeItemBOM'] ?? $planData['MaterialCodeBOM'] ?? null)
                    : null;

                $planningId = DB::table('tb_plan_first_productions')->insertGetId([
                    'code_plan' => $code_plan,
                    'code_item' => $planData['MaterialCode'],
                    'code_machine' => $codeMachine,
                    'code_item_bom' => $codeItemBOM, // Material Code BOM untuk PLONG
                    'quantity' => $planData['Quantity'],
                    'up_cetak' => $planData['UP'] ?? 1,
                    'capacity' => $planData['MachineCapacity'] ?? 1000,
                    'est_jam' => $planData['Estimation'],
                    'est_day' => $planData['Estimation'] / 24, // Konversi jam ke hari
                    'start_jam' => $planData['StartJam'],
                    'end_jam' => $planData['EndJam'],
                    'flag_status' => 'PLANNED',
                    'wo_docno' => $planData['WODocNo'],
                    'so_docno' => $planData['SODocNo'],
                    'delivery_date' => $planData['DeliveryDate'],
                    'created_by' => auth()->user()->name ?? 'SYSTEM',
                    'created_at' => now(),
                    'material_name' => $planData['MaterialName'],
                    'process' => $processType,
                    'department' => $department,
                    // Tambahkan field khusus untuk PPOS jika ada
                    // 'operator' => $isPPOS ? ($planData['operator'] ?? '') : null,
                    // 'notes' => $isPPOS ? ($planData['notes'] ?? '') : null,
                ]);

                $savedItems[] = [
                    'planning_id' => $planningId,
                    'wo_docno' => $planData['WODocNo'],
                    'code_item' => $planData['MaterialCode'],
                    'code_machine' => $codeMachine, // Gunakan $codeMachine yang sudah diset (null untuk PLONG)
                    'start_jam' => $planData['StartJam'],
                    'end_jam' => $planData['EndJam']
                ];
            }

            DB::commit();
            return $savedItems;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Hitung ulang timeline agar sesuai dengan view
     */
    private function recalculateTimelineForView($planPerItem, $startTime, $selectedProcess)
    {
        $recalculatedItems = [];

        // Group items by machine first
        $machineGroups = [];
        foreach ($planPerItem as $uniqueKey => $item) {
            $machine = $item['Machine'];
            if (!isset($machineGroups[$machine])) {
                $machineGroups[$machine] = [];
            }
            $machineGroups[$machine][$uniqueKey] = $item;
        }

        // Ambil semua mesin yang akan digunakan
        $machines = array_keys($machineGroups);

        // Ambil end_jam terakhir dari database untuk setiap mesin
        // Filter berdasarkan process jika proses PLONG
        $machineLastEnd = [];
        if (!empty($machines)) {
            // Ambil dari tb_plan_first_productions
            $query = PlanFirstProduction::whereIn('code_machine', $machines)
                ->whereNotNull('end_jam')
                ->where('flag_status', '!=', 'FINISH');

            // Untuk PLONG, filter juga berdasarkan process PLG
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                $query->whereIn('process', ['PLG', 'PLONG']);
            }

            $existingPlans = $query->orderBy('end_jam', 'desc')
                ->get(['code_machine', 'end_jam']);

            foreach ($existingPlans as $plan) {
                if (!isset($machineLastEnd[$plan->code_machine])) {
                    $machineLastEnd[$plan->code_machine] = Carbon::parse($plan->end_jam);
                }
            }

            // Untuk PLONG, juga cek dari tb_plan_continued_productions
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                $continuedPlans = DB::table('tb_plan_continued_productions')
                    ->whereIn('code_machine', $machines)
                    ->whereIn('process', ['PLG', 'PLONG'])
                    ->whereNotNull('end_jam')
                    ->where('flag_status', '!=', 'FINISHED')
                    ->orderBy('end_jam', 'desc')
                    ->get(['code_machine', 'end_jam']);

                foreach ($continuedPlans as $plan) {
                    $planEndTime = Carbon::parse($plan->end_jam);
                    if (!isset($machineLastEnd[$plan->code_machine])) {
                        $machineLastEnd[$plan->code_machine] = $planEndTime;
                    } else {
                        // Ambil yang lebih besar (lebih akhir)
                        if ($planEndTime->gt($machineLastEnd[$plan->code_machine])) {
                            $machineLastEnd[$plan->code_machine] = $planEndTime;
                        }
                    }
                }
            }
        }

        // Process each machine group separately
        foreach ($machineGroups as $machine => $items) {
            // Tentukan start time untuk mesin ini
            // Jika mesin sudah punya item di database, mulai dari end_jam terakhir
            // Jika tidak, mulai dari startTime (jam 8)
            if (isset($machineLastEnd[$machine])) {
                $currentTime = $machineLastEnd[$machine];
                Log::info("Machine {$machine} has existing plans, starting from last end_jam: " . $currentTime->format('Y-m-d H:i:s'));
            } else {
                $currentTime = Carbon::parse($startTime);
                Log::info("Machine {$machine} has no existing plans, starting from startTime: " . $currentTime->format('Y-m-d H:i:s'));
            }

            foreach ($items as $uniqueKey => $item) {
                // dd($item);
                // Khusus untuk WOP, quantity wajib 0 PCS dan durasi 8 jam
                // LOGIKA:
                // - Preview: Quantity ASLI dari WO (tidak dibagi UP) - sudah benar dari generateSimpleProcessPlan
                // - Save: Quantity untuk database = Quantity WO (PCS) / UP = Quantity dalam LBR
                // Hanya sekali pembagian saja!

                // Ambil quantity ASLI dari WO (jika ada OriginalQuantity, gunakan itu, jika tidak gunakan Quantity)
                $quantityWO = $item['OriginalQuantity'] ?? $item['Quantity']; // Quantity ASLI dari WO (dalam PCS)
                $itemUP = $item['UP'] ?? 1;
                $woUnit = $item['Unit'] ?? 'PCS';
                $machineUnit = $item['MachineUnit'] ?? 'PCS';

                // Quantity untuk database: jika unit berbeda, bagi dengan UP (sekali saja)
                // Jika unit sama, gunakan quantity asli
                $quantityForDatabase = $quantityWO;
                if ($woUnit !== $machineUnit && $itemUP > 1) {
                    $quantityForDatabase = $quantityWO / $itemUP;
                    Log::info("Converting quantity for database: {$quantityWO} {$woUnit} / {$itemUP} = {$quantityForDatabase} {$machineUnit}");
                }

                // Untuk perhitungan estimasi, gunakan quantity yang sudah dikonversi ke unit mesin
                $quantityForEstimation = $quantityForDatabase;

                $estimation = 0;
                $machineCapacity = floatval($item['MachineCapacity'] ?? 1000); // Default capacity

                if (str_starts_with($item['WODocNo'], 'WOP')) {
                    $quantityForEstimation = 0;
                    $estimation = 8 * 3600; // 8 jam dalam detik
                } else {
                    // Hitung estimasi berdasarkan quantity yang sudah dikonversi ke unit mesin
                    if ($machineCapacity > 0) {
                        $estimation = ($quantityForEstimation / $machineCapacity) * 3600; // Convert ke detik
                    } else {
                        // Fallback jika kapasitas 0 (misalnya untuk PPOS)
                        $estimation = $item['Estimation'] * 3600; // Gunakan estimation yang sudah ada
                    }
                }

                // Set waktu start dan end untuk item ini
                $itemStartTime = $currentTime;
                $itemEndTime = Carbon::parse($currentTime)->addSeconds($estimation);

                // Update data dengan timeline yang benar
                // IMPORTANT: Simpan quantity yang sudah dikonversi ke unit mesin (LBR) ke database
                // Quantity untuk database = Quantity WO (PCS) / UP = Quantity dalam LBR
                $recalculatedItems[$uniqueKey] = [
                    'MaterialCode' => $item['MaterialCode'],
                    'MaterialName' => $item['MaterialName'],
                    'Quantity' => $quantityForDatabase, // Quantity yang sudah dikonversi ke unit mesin (LBR)
                    'WODocNo' => $item['WODocNo'],
                    'SODocNo' => $item['SODocNo'],
                    'Proses' => $selectedProcess,
                    'Machine' => $item['Machine'],
                    'Estimation' => $estimation / 3600, // Convert kembali ke jam untuk display
                    'StartJam' => $itemStartTime->format('Y-m-d H:i:s'), // Format yang sesuai database
                    'EndJam' => $itemEndTime->format('Y-m-d H:i:s'), // Format yang sesuai database
                    'DeliveryDate' => $item['DeliveryDate'] ?? now()->format('Y-m-d'),
                    'order' => $item['order'] ?? 1,
                    'Unit' => $item['Unit'] ?? 'PCS',
                    'UP' => $item['UP'] ?? 1,
                    'MachineUnit' => $item['MachineUnit'] ?? 'PCS',
                    'MachineCapacity' => $machineCapacity,
                    'ConvertedQuantity' => $quantityForEstimation, // Quantity yang sudah dikonversi untuk estimasi
                    'ConversionApplied' => ($woUnit !== $machineUnit && $itemUP > 1),
                    // Untuk PLONG: pertahankan CodeItemBOM dan MaterialCodeBOM
                    'CodeItemBOM' => $item['CodeItemBOM'] ?? $item['MaterialCodeBOM'] ?? null,
                    'MaterialCodeBOM' => $item['CodeItemBOM'] ?? $item['MaterialCodeBOM'] ?? null
                ];

                // Update waktu untuk item berikutnya dalam mesin yang sama
                $currentTime = $itemEndTime;
            }
        }

        // dd($recalculatedItems);

        return $recalculatedItems;
    }

    /**
     * Generate simple process plan untuk proses yang dipilih
     */
    private function generateSimpleProcessPlan($item, $selectedProcess, $startTime)
    {
        // dd($item);
        try {
            // Default values untuk item yang tidak memiliki field tertentu
            $materialCode = $item['MaterialCode'] ?? '';
            $materialName = $item['MaterialName'] ?? $item['Name'] ?? 'Unknown Material';

            // Untuk PLONG: machine harus null (tidak ambil dari mapping)
            if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                $machine = $this->getDefaultMachineForProcess($selectedProcess, $materialCode, null);
                $machineDetail = Machine::where('Code', $machine)->first();
                // Proses Cetak
                $unitMachine = $machineDetail ? $machineDetail->Unit : 'PCS';
                $unitData = $item['Unit'];

                // dd($unitMachine, $unitData);
                $quantity = intval($item['Quantity'] ?? 1);

                // if ($unitMachine !== $unitData) {
                //     $quantity = $quantity / $item['UP'];
                // }

                $woDocNo = $item['WODocNo'] ?? '';
                $soDocNo = $item['SODocNo'] ?? '';
                $woUnit = $item['Unit'] ?? 'PCS';
                $woUp = intval($item['UP'] ?? 1);

                // dd($materialCode);
                // Ambil mesin dari tb_mapping_items berdasarkan material code

                // dd($machine);

                // Cari kapasitas dan unit mesin dari mastermachine di mysql3
                $machineData = Machine::where('Code', $machine)->first();
                $machineCapacity = $machineData ? floatval($machineData->CapacityPerHour) : 1000;
                $machineUnit = $machineData ? $machineData->Unit : 'PCS';
            } else {
                $machine = $this->getDefaultMachineForProcess($selectedProcess, $materialCode, null);
                $machineDetail = Machine::where('Code', $machine)->first();
                // Proses Cetak
                $unitMachine = $machineDetail ? $machineDetail->Unit : 'PCS';
                $unitData = $item['Unit'];

                // dd($unitMachine, $unitData);
                $quantity = intval($item['Quantity'] ?? 1);

                // if ($unitMachine !== $unitData) {
                //     $quantity = $quantity / $item['UP'];
                // }

                $woDocNo = $item['WODocNo'] ?? '';
                $soDocNo = $item['SODocNo'] ?? '';
                $woUnit = $item['Unit'] ?? 'PCS';
                $woUp = intval($item['UP'] ?? 1);

                // dd($materialCode);
                // Ambil mesin dari tb_mapping_items berdasarkan material code

                // dd($machine);

                // Cari kapasitas dan unit mesin dari mastermachine di mysql3
                $machineData = Machine::where('Code', $machine)->first();
                $machineCapacity = $machineData ? floatval($machineData->CapacityPerHour) : 1000;
                $machineUnit = $machineData ? $machineData->Unit : 'PCS';
            }

            // Variabel yang digunakan di bawah (untuk semua proses)
            $quantity = intval($item['Quantity'] ?? 1);
            $woDocNo = $item['WODocNo'] ?? '';
            $soDocNo = $item['SODocNo'] ?? '';
            $woUnit = $item['Unit'] ?? 'PCS';
            $woUp = intval($item['UP'] ?? 1);
            // dd($machineCapacity, $machineUnit);
            // }
            // } catch (Exception $e) {
            //     Log::warning("Failed to get machine data from mysql3 for machine {$machine}: " . $e->getMessage());
            // }

            // Khusus untuk WOP, quantity wajib 0 PCS dan durasi 8 jam
            $convertedQuantity = $quantity;
            $conversionApplied = false;
            $estimation = 0;

            // Pastikan convertedQuantity diinisialisasi dengan benar
            if (!$conversionApplied) {
                $convertedQuantity = $quantity;
            }

            if (str_starts_with($woDocNo, 'WOP')) {
                $convertedQuantity = 0;
                $estimation = 8; // 8 jam untuk WOP
                $conversionApplied = true;
                Log::info("WOP detected for {$materialCode}: quantity set to 0, duration set to 8 hours");
            } else {
                // Konversi quantity jika unit berbeda untuk non-WOP
                if ($woUnit !== $machineUnit && $woUp > 1) {
                    $convertedQuantity = $quantity / $woUp;
                    $conversionApplied = true;
                    Log::info("Unit conversion applied for {$materialCode}: {$quantity} {$woUnit} / {$woUp} = {$convertedQuantity} {$machineUnit}");
                }

                // Khusus untuk PLONG: gunakan quantity asli tanpa konversi jika unit sama
                // atau konversi yang sudah dilakukan di atas
                if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                    // Untuk PLONG, quantity biasanya sudah dalam unit yang benar
                    // Pastikan convertedQuantity sudah benar
                    if (!$conversionApplied && $woUnit === $machineUnit) {
                        $convertedQuantity = $quantity;
                    }

                    // Log untuk debugging PLONG
                    Log::info("PLONG calculation for {$materialCode}:", [
                        'original_quantity' => $quantity,
                        'converted_quantity' => $convertedQuantity,
                        'wo_unit' => $woUnit,
                        'machine_unit' => $machineUnit,
                        'wo_up' => $woUp,
                        'conversion_applied' => $conversionApplied,
                        'machine_capacity' => $machineCapacity
                    ]);
                }

                // dd('quantity', $quantity, 'convertedQuantity', $convertedQuantity);

                // Hitung estimasi waktu berdasarkan quantity yang sudah dikonversi
                if ($machineCapacity > 0) {
                    $estimation = $convertedQuantity / $machineCapacity;

                    // Validasi estimasi untuk PLONG - pastikan tidak terlalu besar
                    if (strtoupper($selectedProcess) === 'PLONG' || strtoupper($selectedProcess) === 'PLG') {
                        // Jika estimasi terlalu besar (lebih dari 1000 jam), kemungkinan ada masalah
                        if ($estimation > 1000) {
                            Log::warning("PLONG estimation too large for {$materialCode}: {$estimation} hours. Quantity: {$convertedQuantity}, Capacity: {$machineCapacity}");
                            // Coba gunakan base estimation untuk PLONG (0.4 jam per item)
                            $estimation = max(0.4, $convertedQuantity / 10000); // Fallback dengan capacity 10000
                        }
                    }
                } else {
                    $estimation = 1; // Fallback minimal 1 jam jika kapasitas 0
                    Log::warning("Machine capacity is 0 for {$materialCode}, using fallback estimation: {$estimation} hours");
                }

                // Validasi estimasi
                if ($estimation <= 0 || is_nan($estimation) || is_infinite($estimation)) {
                    $estimation = 1; // Fallback minimal 1 jam
                    Log::warning("Invalid estimation calculated for {$materialCode}, using fallback: {$estimation} hours");
                }
            }

            if (str_starts_with($woDocNo, 'WOP')) {
                Log::info("WOP time calculation for {$materialCode}: quantity = {$convertedQuantity}, duration = {$estimation} hours (hardcoded)");
            } else {
                Log::info("Time calculation for {$materialCode}: {$convertedQuantity} {$machineUnit} / {$machineCapacity} = {$estimation} hours");
            }

            // dd($convertedQuantity);

            // Hitung start dan end time
            $startTime = clone $startTime;
            $endTime = (clone $startTime)->addHours($estimation);

            // LOGIKA:
            // - Untuk PREVIEW: Quantity harus ASLI (tidak dibagi UP) - tampilkan quantity dari WO
            // - Untuk SAVE: Quantity dibagi UP sekali saja (Quantity WO / UP)
            // Jadi kembalikan quantity ASLI untuk preview, konversi dilakukan saat save

            return [
                'MaterialCode' => $materialCode,
                'MaterialName' => $materialName,
                'Quantity' => $quantity, // Quantity ASLI dari WO (untuk preview), tidak dibagi UP
                'WODocNo' => $woDocNo,
                'SODocNo' => $soDocNo,
                'Proses' => $selectedProcess,
                'Machine' => $machine,
                'Estimation' => $estimation,
                'StartJam' => $startTime->format('Y-m-d H:i:s'),
                'EndJam' => $endTime->format('Y-m-d H:i:s'),
                'DeliveryDate' => $item['DeliveryDate'] ?? $startTime->format('Y-m-d'),
                'order' => $item['order'] ?? 1,
                'Unit' => $woUnit,
                'UP' => $woUp,
                'MachineUnit' => $machineUnit,
                'MachineCapacity' => $machineCapacity,
                'ConvertedQuantity' => $convertedQuantity, // Quantity yang sudah dikonversi untuk estimasi
                'ConversionApplied' => $conversionApplied,
                'OriginalQuantity' => $quantity, // Simpan quantity asli untuk referensi
                // Untuk PLONG: tambahkan CodeItemBOM jika ada
                'CodeItemBOM' => $item['CodeItemBOM'] ?? $item['MaterialCodeBOM'] ?? null,
                'MaterialCodeBOM' => $item['CodeItemBOM'] ?? $item['MaterialCodeBOM'] ?? null // Alias
            ];
        } catch (Exception $e) {
            Log::error('Error generating simple process plan:', [
                'item' => $item,
                'selectedProcess' => $selectedProcess,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get default machine untuk proses tertentu
     */
    private function getDefaultMachineForProcess($process, $materialCode = null, $department = null)
    {

        // dd($process);
        // Jika ada materialCode, cari dari tb_mapping_items
        if ($materialCode) {
            try {
                $mapping = null;

                if (in_array(strtoupper($process), ['CETAK', 'CTK'])) {
                    // Untuk proses CETAK, cari field m_ctk
                    $mapping = DB::table('tb_mapping_items')
                        ->where('kode', $materialCode)
                        ->whereNotNull('m_ctk')
                        ->where('m_ctk', '!=', '')
                        ->first();

                    // dd($mapping);

                    if ($mapping) {
                        return $mapping->m_ctk;
                    }
                } elseif (in_array(strtoupper($process), ['POTONG', 'PTG'])) {
                    // Untuk proses POTONG, cari field m_ptg
                    $mapping = DB::table('tb_mapping_items')
                        ->where('kode', $materialCode)
                        ->whereNotNull('m_ptg')
                        ->where('m_ptg', '!=', '')
                        ->first();

                    if ($mapping) {
                        return $mapping->m_ptg;
                    }
                } elseif (in_array(strtoupper($process), ['PLONG', 'PLG'])) {
                    // Untuk proses PLONG, cari field m_plg
                    $mapping = DB::table('tb_mapping_items')
                        ->where('kode', $materialCode)
                        ->whereNotNull('m_plg')
                        ->where('m_plg', '!=', '')
                        ->first();

                    if ($mapping) {
                        $machineCode = $mapping->m_plg;

                        // Jika ada department, validasi mesin sesuai department
                        if ($department) {
                            $machine = Machine::where('Code', $machineCode)
                                ->where('Department', $department)
                                ->first();

                            if ($machine) {
                                Log::info("Machine mapping found for PLONG process with department match", [
                                    'materialCode' => $materialCode,
                                    'machine' => $machineCode,
                                    'department' => $department,
                                    'process' => $process
                                ]);
                                return $machineCode;
                            } else {
                                // Jika mesin tidak sesuai department, cari mesin PLONG lain dari department yang sama
                                $alternativeMachine = Machine::where('Department', $department)
                                    ->where('Code', 'LIKE', 'PLG%')
                                    ->where('Description', 'not like', '%JANGAN DIPAKAI%')
                                    ->first();

                                if ($alternativeMachine) {
                                    Log::info("Using alternative PLONG machine from same department", [
                                        'materialCode' => $materialCode,
                                        'original_machine' => $machineCode,
                                        'alternative_machine' => $alternativeMachine->Code,
                                        'department' => $department
                                    ]);
                                    return $alternativeMachine->Code;
                                }

                                Log::warning("Machine mapping found but not in department, and no alternative found", [
                                    'materialCode' => $materialCode,
                                    'machine' => $machineCode,
                                    'department' => $department,
                                    'process' => $process
                                ]);
                            }
                        } else {
                            Log::info("Machine mapping found for PLONG process (no department filter)", [
                                'materialCode' => $materialCode,
                                'machine' => $machineCode,
                                'process' => $process
                            ]);
                            return $machineCode;
                        }
                    } else {
                        Log::warning("No machine mapping (m_plg) found for PLONG process", [
                            'materialCode' => $materialCode,
                            'process' => $process
                        ]);
                    }
                } elseif (in_array(strtoupper($process), ['EMBOSS', 'EMB'])) {
                    // Untuk proses EMBOSS, cari field m_emb
                    $mapping = DB::table('tb_mapping_items')
                        ->where('kode', $materialCode)
                        ->whereNotNull('m_emb')
                        ->where('m_emb', '!=', '')
                        ->first();

                    if ($mapping) {
                        return $mapping->m_emb;
                    }
                } elseif (in_array(strtoupper($process), ['SORTIR', 'STR'])) {
                    // Untuk proses SORTIR, cari field m_str
                    $mapping = DB::table('tb_mapping_items')
                        ->where('kode', $materialCode)
                        ->whereNotNull('m_str')
                        ->where('m_str', '!=', '')
                        ->first();

                    if ($mapping) {
                        return $mapping->m_str;
                    }
                } elseif (in_array(strtoupper($process), ['GLUEING', 'GLU', 'LEM'])) {
                    // Untuk proses GLUEING/LEM, cari field m_lem
                    $mapping = DB::table('tb_mapping_items')
                        ->where('kode', $materialCode)
                        ->whereNotNull('m_lem')
                        ->where('m_lem', '!=', '')
                        ->first();

                    if ($mapping) {
                        return $mapping->m_lem;
                    }
                }

                Log::info("No machine mapping found for material {$materialCode} and process {$process}");
            } catch (Exception $e) {
                Log::error("Error getting machine mapping for material {$materialCode} and process {$process}: " . $e->getMessage());
            }
        }

        // Fallback ke default machine jika tidak ada mapping atau error
        // Jika ada department, cari mesin dari department yang sama
        if ($department && in_array(strtoupper($process), ['PLONG', 'PLG'])) {
            $defaultMachine = Machine::where('Department', $department)
                ->where('Code', 'LIKE', 'PLG%')
                ->where('Description', 'not like', '%JANGAN DIPAKAI%')
                ->first();

            if ($defaultMachine) {
                Log::info("Using default PLONG machine from department", [
                    'department' => $department,
                    'machine' => $defaultMachine->Code,
                    'process' => $process
                ]);
                return $defaultMachine->Code;
            }
        }
        // dd($process);

        if ($department && in_array(strtoupper($process), ['GLUEING', 'GLU', 'LEM'])) {
            $defaultMachine = Machine::where('Department', $department)
                // ->where('Code', 'LIKE', 'LEM%')
                ->where('Description', 'not like', '%JANGAN DIPAKAI%')
                ->first();

            if ($defaultMachine) {
                Log::info("Using default LEM machine from department", [
                    'department' => $department,
                    'machine' => $defaultMachine->Code,
                    'process' => $process
                ]);
                return $defaultMachine->Code;
            }
        }


        $machineMap = [
            'CETAK' => 'CTK-001',
            'CTK' => 'CTK-001',
            'POTONG' => 'PTG-001',
            'PTG' => 'PTG-001',
            'PLONG' => 'PLG-001',
            'PLG' => 'PLG-001',
            'EMBOSS' => 'EMB-001',
            'EMB' => 'EMB-001',
            'SORTIR' => 'STR-001',
            'STR' => 'STR-001',
            'GLUEING' => 'TIDAK TERSEDIA 1',
            'GLU' => 'TIDAK TERSEDIA',
            'LEM' => 'LEM-001'
        ];

        return $machineMap[strtoupper($process)] ?? 'DEFAULT-001';
    }

    /**
     * Calculate estimation untuk proses tertentu
     */
    private function calculateEstimationForProcess($process, $quantity)
    {
        // Default estimation dalam jam
        $baseEstimation = [
            'CETAK' => 0.5,
            'CTK' => 0.5,
            'POTONG' => 0.3,
            'PTG' => 0.3,
            'PLONG' => 0.4,
            'PLG' => 0.4,
            'EMBOSS' => 0.6,
            'EMB' => 0.6,
            'SORTIR' => 0.2,
            'STR' => 0.2,
            'GLUEING' => 0.8,
            'GLU' => 0.8,
            'LEM' => 0.8
        ];

        $baseTime = $baseEstimation[strtoupper($process)] ?? 1.0;

        // Tambahkan waktu berdasarkan quantity (setiap 100 item tambah 0.1 jam)
        $additionalTime = floor($quantity / 100) * 0.1;

        return max(0.5, $baseTime + $additionalTime); // Minimal 0.5 jam
    }

    private function continuePlanProcessing($detailProsesItem, $startDate, $code_plan)
    {
        // dd($detailProsesItem);

        Log::info('BACKEND FIX: continuePlanProcessing received items', [
            'total_items' => count($detailProsesItem),
            'item_keys' => array_keys($detailProsesItem)
        ]);

        $prosesGrouped = [];

        // Langsung gunakan proses yang sudah ada di $item['processes'] tanpa menambah proses baru
        foreach ($detailProsesItem as $uniqueKey => $item) {
            // dd($item);
            if (isset($item['processes']) && is_array($item['processes'])) {
                foreach ($item['processes'] as $proses) {
                    $namaProses = $proses['proses'];

                    if (!isset($prosesGrouped[$namaProses])) {
                        $prosesGrouped[$namaProses] = [];
                    }

                    // Khusus untuk WOP, quantity wajib 0 PCS
                    $quantity = $item['Quantity'];
                    if (isset($item['WODocNo']) && str_starts_with($item['WODocNo'], 'WOP')) {
                        $quantity = 0;
                    } elseif ($item['Quantity'] <= 500) {
                        $quantity = 500;
                    }

                    $dataProses = [
                        'MaterialCode' => $uniqueKey, // Gunakan uniqueKey (MaterialCode_WODocNo) untuk identifikasi unik
                        'MaterialName' => $item['MaterialName'],
                        'Machine' => $proses['machine'],
                        'Estimation' => $proses['estimation'],
                        'Formula' => $proses['formula'],
                        'Quantity' => $quantity,
                        'DeliveryDate' => $item['DeliveryDate'],
                        'StartJam' => '',
                        'EndJam' => '',
                        'SODocNo' => $proses['SODocNo'] ?? null,
                        'WODocNo' => $proses['WODocNo'] ?? null,
                    ];

                    if ($namaProses == 'CTK') {
                        // Extract MaterialCode asli dari uniqueKey (format: MaterialCode_WODocNo)
                        $actualMaterialCode = explode('_', $uniqueKey)[0];
                        $jmlWarna = \App\Models\MappingItem::where('kode', $actualMaterialCode)->pluck('jumlah_warna')->first();
                        $warnaDetail = \App\Models\MappingItem::where('kode', $actualMaterialCode)->first();

                        $warnaTerpilih = [];
                        for ($i = 1; $i <= 15; $i++) {
                            $key = 't' . $i;
                            if (isset($warnaDetail->$key)) {
                                $warnaTerpilih[$key] = $warnaDetail->$key;
                            }
                        }

                        $dataProses['JumlahWarna'] = $jmlWarna;
                        $dataProses['Warna'] = $warnaTerpilih;
                    }

                    $prosesGrouped[$namaProses][] = $dataProses;
                }
            }
        }

        // dd($prosesGrouped);

        // Urutkan setiap grup proses berdasarkan DeliveryDate
        foreach ($prosesGrouped as $namaProses => &$group) {
            usort($group, function ($a, $b) {
                return strtotime($a['DeliveryDate']) <=> strtotime($b['DeliveryDate']);
            });
        }

        // dd($prosesGrouped);

        // Jika proses CTK, urutkan lagi berdasarkan kemiripan warna
        if (isset($prosesGrouped['CTK'])) {
            // Fungsi kemiripan warna
            $warnaSimilarity = function ($a, $b) {
                $count = 0;
                if (!isset($a['Warna']) || !isset($b['Warna'])) return 0;
                foreach ($a['Warna'] as $key => $warnaA) {
                    if (!empty($warnaA) && isset($b['Warna'][$key]) && $warnaA === $b['Warna'][$key]) {
                        $count++;
                    }
                }
                return $count;
            };
            // Greedy Nearest Neighbor
            $ctkArray = $prosesGrouped['CTK'];
            $resultCtk = [];
            $used = [];
            $n = count($ctkArray);
            if ($n > 0) {
                $currentIdx = 0;
                $resultCtk[] = $ctkArray[$currentIdx];
                $used[$currentIdx] = true;
                for ($i = 1; $i < $n; $i++) {
                    $maxSim = -1;
                    $nextIdx = -1;
                    foreach ($ctkArray as $idx => $item) {
                        if (isset($used[$idx])) continue;
                        $sim = $warnaSimilarity($resultCtk[count($resultCtk) - 1], $item);
                        if ($sim > $maxSim) {
                            $maxSim = $sim;
                            $nextIdx = $idx;
                        }
                    }
                    $resultCtk[] = $ctkArray[$nextIdx];
                    $used[$nextIdx] = true;
                }
                $prosesGrouped['CTK'] = $resultCtk;
            }
            // Penjadwalan start/end jam untuk CTK
            $startDateCtk = Carbon::parse($startDate)->setTime(8, 0, 0);
            $currentStart = clone $startDateCtk;
            $prevMaterialCode = null;
            foreach ($prosesGrouped['CTK'] as $idx => &$ctkItem) {
                $setupSeconds = 0;
                if ($prevMaterialCode !== null && $ctkItem['MaterialCode'] !== $prevMaterialCode) {
                    $setupSeconds = 3600;
                }
                $breaks = [
                    ['start' => 12, 'end' => 13],
                    ['start' => 19, 'end' => 20],
                    ['start' => 1, 'end' => 3],
                ];
                $estSeconds = $ctkItem['Estimation'] * 3600;
                $totalSeconds = $estSeconds + $setupSeconds;
                $start = clone $currentStart;
                $end = (clone $start)->addSeconds($totalSeconds);
                $breakSeconds = 0;
                $ctkItem['BreakAdded'] = [];
                // Hitung break hanya jika benar-benar dilewati oleh proses
                $processStart = clone $start;
                $processEnd = clone $end;
                $breakCount = 0;
                $ctkItem['BreakAdded'] = [];
                foreach ($breaks as $break) {
                    // Tentukan waktu break di hari proses mulai
                    $breakStart = (clone $processStart)->setTime($break['start'], 0, 0);
                    $breakEnd = (clone $processStart)->setTime($break['end'], 0, 0);
                    if ($breakEnd->lessThan($breakStart)) {
                        $breakEnd->addDay();
                    }
                    // Jika proses melewati jam break
                    if ($processStart->lt($breakEnd) && $processEnd->gt($breakStart)) {
                        $breakCount++;
                        $ctkItem['BreakAdded'][] = $break['start'] . '-' . $break['end'];
                    }
                    // Jika proses lebih dari 1 hari, cek break di hari berikutnya
                    $spanDays = $processStart->diffInDays($processEnd);
                    for ($d = 1; $d <= $spanDays; $d++) {
                        $breakStartDay = (clone $breakStart)->addDays($d);
                        $breakEndDay = (clone $breakEnd)->addDays($d);
                        if ($processStart->lt($breakEndDay) && $processEnd->gt($breakStartDay)) {
                            $breakCount++;
                            $ctkItem['BreakAdded'][] = $break['start'] . '-' . $break['end'];
                        }
                    }
                }
                $breakSeconds = $breakCount * 3600;
                $end->addSeconds($breakSeconds);
                $ctkItem['StartJam'] = $start->format('Y-m-d H:i:s');
                $ctkItem['EndJam'] = $end->format('Y-m-d H:i:s');
                $ctkItem['Setup'] = $setupSeconds > 0 ? 1 : 0;
                $ctkItem['SetupSeconds'] = $setupSeconds;
                $ctkItem['BreakSeconds'] = $breakSeconds;
                $currentStart = $end;
                $prevMaterialCode = $ctkItem['MaterialCode'];
            }
        }

        // dd($detailProsesItem);


        // Penjadwalan berurutan untuk semua proses per item sesuai urutan training
        $planPerItem = [];
        $trainingOrders = [
            ['PTG', 'LMG', 'CTK', 'EPL', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'LMG', 'PLG', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'LMG', 'UV1', 'EPL', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'HP', 'UV1', 'EMB', 'PLG', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'HP', 'UV', 'EPL', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'PLG', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'EPL', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'WBV', 'PLG', 'KPS', 'STR', 'LEM'],
            ['PTG', 'LMG', 'CTK', 'EM1', 'EPL', 'KPS', 'STR', 'LEM'],
            ['PLG', 'CTK', 'LMS', 'PLG', 'KPS', 'STR', 'LEM'],
            ['PTG', 'CTK', 'CAL', 'PLG', 'KPS', 'STR', 'LEM'],
        ];

        // FIX: Gunakan key yang sama dengan submitPlanFirst (MaterialCode_WODocNo)
        foreach ($detailProsesItem as $uniqueKey => $item) {
            // Extract MaterialCode asli dari uniqueKey (format: MaterialCode_WODocNo)
            $materialCode = explode('_', $uniqueKey)[0];

            // Gunakan algoritma canggih untuk menemukan urutan terbaik
            $bestOrder = $this->findBestProcessOrder($item['processes'], $trainingOrders);

            // Susun proses sesuai bestOrder
            $orderedProses = [];
            foreach ($bestOrder as $procName) {
                foreach ($item['processes'] as $p) {
                    if ($p['proses'] == $procName) {
                        $orderedProses[] = $p;
                    }
                }
            }
            // Tambahkan proses yang tidak ada di training di akhir
            foreach ($item['processes'] as $p) {
                if (!in_array($p, $orderedProses)) {
                    $orderedProses[] = $p;
                }
            }
            // Tambahkan filter agar hanya satu PTG dan satu TUM
            $orderedProses = $this->filterSinglePTGandTUM($orderedProses);
            // Penjadwalan serial: proses pertama mulai dari $startDate jam 08:00, berikutnya dari EndJam proses sebelumnya
            $prevEnd = Carbon::parse($startDate)->setTime(8, 0, 0);
            foreach ($orderedProses as $proses) {
                $machine = $proses['machine'];
                $machineData = Machine::where('Code', $machine)
                    ->first();
                // $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $machine)->first();
                $capacityPerHour = $machineData && isset($machineData->CapacityPerHour) ? $machineData->CapacityPerHour : 1;
                $quantity = $item['Quantity'];
                $estimation = $quantity / $capacityPerHour;
                $estSeconds = $estimation * 3600;
                $start = clone $prevEnd;
                $end = (clone $start)->addSeconds($estSeconds);
                $setup = 0;
                $break = 0;

                // FIX: Gunakan uniqueKey (MaterialCode_WODocNo) sebagai key untuk planPerItem
                $planPerItem[$uniqueKey][] = [
                    'MaterialCode' => $item['MaterialCode'],
                    'MaterialName' => $item['MaterialName'],
                    'Machine' => $machine,
                    'Proses' => $proses['proses'],
                    'StartJam' => $start->format('Y-m-d H:i:s'),
                    'EndJam' => $end->format('Y-m-d H:i:s'),
                    'SetupSeconds' => $setup,
                    'BreakSeconds' => $break,
                    'Estimation' => $estimation,
                    'Quantity' => $quantity,
                    'Formula' => $proses['formula'],
                    'SODocNo' => $item['SODocNo'] ?? null,
                    'WODocNo' => $materialCode ?? null,
                ];
                $prevEnd = $end;
            }
        }

        // dd('ini planPerItem 1', $planPerItem);

        // Kelompokkan berdasarkan proses dan mesin, hitung ulang start_jam_new dan end_jam_new
        $planPerItem = $this->groupAndRecalculateMachineSchedule($planPerItem, $startDate);

        // Tampilkan pengelompokkan berdasarkan proses dan mesin
        $groupedByMachine = $this->displayGroupedByMachine($planPerItem);

        // dd('ini planPerItem 2', $planPerItem);
        // dd('ini pla', $planPerItem);

        // Tampilkan preview sebelum simpan ke database
        Log::info('Sending preview response from continuePlanProcessing with data:', [
            'planPerItem_count' => count($planPerItem),
            'groupedByMachine_count' => count($groupedByMachine),
            'planPerItem_keys' => array_keys($planPerItem),
            'sample_data' => array_slice($planPerItem, 0, 2, true) // Ambil 2 sample data
        ]);

        if (count($planPerItem) > 0 && count($groupedByMachine) > 0) {
            return response()->json([
                'success' => false,
                'preview' => true,
                'ptg_tum_completed' => true, // Tanda khusus bahwa PTG/TUM sudah selesai
                'message' => 'Preview rencana produksi berhasil dibuat',
                'data' => [
                    'planPerItem' => $planPerItem,
                    'groupedByMachine' => $groupedByMachine
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'preview' => false,
                'ptg_tum_completed' => true, // Tanda khusus bahwa PTG/TUM sudah selesai
                'message' => 'Preview rencana produksi gagal dibuat',
            ]);
        }
    }

    private function findBestProcessOrder($itemProcesses, $trainingOrders)
    {
        $bestOrder = null;
        $bestScore = -1;

        // Validasi input
        if (empty($itemProcesses) || empty($trainingOrders)) {
            return $trainingOrders[0] ?? [];
        }

        $itemNames = array_map(function ($p) {
            return $p['proses'];
        }, $itemProcesses);

        foreach ($trainingOrders as $order) {
            $score = $this->calculateOrderScore($itemNames, $order);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestOrder = $order;
            }
        }

        return $bestOrder ?? $trainingOrders[0] ?? [];
    }

    /**
     * Urutkan proses sesuai bestOrder dan hanya ambil satu PTG/TUM.
     * @param array $processes
     * @param array $bestOrder
     * @return array
     */
    private function filterAndOrderProcesses($processes, $bestOrder)
    {
        $result = [];
        $ptgAdded = false;
        $tumAdded = false;
        foreach ($bestOrder as $procName) {
            foreach ($processes as $p) {
                if ($p['proses'] === $procName) {
                    if ($procName === 'PTG') {
                        if (!$ptgAdded) {
                            $result[] = $p;
                            $ptgAdded = true;
                            break;
                        }
                    } elseif ($procName === 'TUM') {
                        if (!$tumAdded) {
                            $result[] = $p;
                            $tumAdded = true;
                            break;
                        }
                    } else {
                        $result[] = $p;
                        break;
                    }
                }
            }
        }
        // Tambahkan proses yang tidak ada di bestOrder di akhir
        foreach ($processes as $p) {
            if (!in_array($p, $result, true)) {
                $result[] = $p;
            }
        }
        return $result;
    }

    /**
     * Ambil urutan proses dari array processes, hanya satu PTG dan satu TUM.
     * @param array $processes
     * @return array
     */
    private function getFilteredProcessNames($processes)
    {
        $filtered = [];
        $ptgAdded = false;
        $tumAdded = false;
        foreach ($processes as $p) {
            if ($p['proses'] === 'PTG') {
                if (!$ptgAdded) {
                    $filtered[] = $p['proses'];
                    $ptgAdded = true;
                }
            } elseif ($p['proses'] === 'TUM') {
                if (!$tumAdded) {
                    $filtered[] = $p['proses'];
                    $tumAdded = true;
                }
            } else {
                $filtered[] = $p['proses'];
            }
        }
        return $filtered;
    }

    /**
     * Cari index trainingOrders yang paling mirip dengan urutan proses (hanya satu PTG/TUM).
     * @param array $processes
     * @param array $trainingOrders
     * @return int
     */
    private function getBestTrainingOrderIndex($processes, $trainingOrders)
    {
        $processNames = $this->getFilteredProcessNames($processes);
        $bestIdx = 0;
        $bestScore = -1;
        foreach ($trainingOrders as $idx => $order) {
            $score = $this->lcsScore($processNames, $order);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIdx = $idx;
            }
        }
        return $bestIdx;
    }

    /**
     * LCS Score (Longest Common Subsequence) antara dua array.
     */
    private function lcsScore($a, $b)
    {
        $m = count($a);
        $n = count($b);
        $dp = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));
        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                if ($a[$i - 1] === $b[$j - 1]) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }
        return $dp[$m][$n];
    }

    // Contoh pemakaian:
    // $idx = $this->getBestTrainingOrderIndex($item['processes'], $trainingOrders);
    // $bestOrder = $trainingOrders[$idx];

    private function calculateOrderScore($itemNames, $trainingOrder)
    {
        $score = 0;

        // Validasi input
        if (empty($itemNames) || empty($trainingOrder)) {
            return 0;
        }

        // 1. LCS (Longest Common Subsequence) Score
        $lcsLength = $this->calculateLCS($itemNames, $trainingOrder);
        $score += $lcsLength * 2; // Weight LCS lebih tinggi

        // 2. Positional Weight (proses di awal lebih penting)
        $positionalWeight = 0;
        foreach ($itemNames as $process) {
            $pos = array_search($process, $trainingOrder);
            if ($pos !== false) {
                $positionalWeight += (count($trainingOrder) - $pos) / count($trainingOrder);
            }
        }
        $score += $positionalWeight;

        // 3. Continuity Bonus (proses berurutan lebih baik)
        $matchedPositions = [];
        foreach ($itemNames as $process) {
            $pos = array_search($process, $trainingOrder);
            if ($pos !== false) {
                $matchedPositions[] = $pos;
            }
        }

        if (!empty($matchedPositions)) {
            sort($matchedPositions);
            $continuityBonus = 0;
            for ($i = 1; $i < count($matchedPositions); $i++) {
                if (
                    isset($matchedPositions[$i]) && isset($matchedPositions[$i - 1]) &&
                    $matchedPositions[$i] - $matchedPositions[$i - 1] == 1
                ) {
                    $continuityBonus += 0.5;
                }
            }
            $score += $continuityBonus;
        }

        // 4. Length Similarity
        $lengthDiff = abs(count($itemNames) - count($trainingOrder));
        $score -= $lengthDiff * 0.3;

        return $score;
    }

    private function calculateLCS($seq1, $seq2)
    {
        // Validasi input
        if (empty($seq1) || empty($seq2)) {
            return 0;
        }

        $m = count($seq1);
        $n = count($seq2);

        $dp = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                // Validasi index sebelum akses
                if (isset($seq1[$i - 1]) && isset($seq2[$j - 1]) && $seq1[$i - 1] === $seq2[$j - 1]) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }

        return $dp[$m][$n];
    }

    private function getMachineCTK($formula)
    {
        $machine = MappingItem::where('kode', $formula)->first();

        if ($machine) {
            $detailMachine = Machine::where('Code', 'like', '%' . $machine->m_ctk . '%')
                ->first();
            // $detailMachine = DB::connection('mysql3')
            // $detailMachine = DB::connection('mysql3')
            //     ->table('mastermachine')
            //     ->where('Code', 'like', '%' . $machine->m_ctk . '%')
            //     ->first();
        }

        return $detailMachine;
    }

    public function viewPlanFistProduction()
    {
        $data = PlanFirstProduction::orderBy('code_machine')->orderBy('start_jam')->get();
        // Group by mesin, lalu group by tanggal
        $grouped = [];
        foreach ($data as $row) {
            $mesin = $row->mesin;
            $tgl = substr($row->start_jam, 0, 10);
            $grouped[$mesin][$tgl][] = $row;
        }
        return view('main.process.planfirstproduction-view', compact('grouped'));
    }

    public function indexPlanMingguan()
    {
        $downtime = '';

        return view('main.process.plan-mingguan', compact('downtime'));
    }

    public function dataPelumasanMaintenance(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {

            $query = PelumasanMaintenance::all();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('date_prob', function ($machines) {
                    return Carbon::parse($machines->date_prob)->format('Y-m-d');
                })
                ->rawColumns(['date_prob'])
                ->make(true);
        }

        return view('main.process.plan-mingguan');
    }

    public function dataPelumasanMaintenanceTb(Request $request)
    {
        // dd($request->all());
        $data = PelumasanMaintenance::all();

        return response()->json(['data' => $data]);
    }

    public function getDataPlanFirst($code)
    {
        // dd($code);
        $data = PlanFirstProduction::where('code_plan', $code)->get();
        $c_machine = $data[0]->code_machine;
        $first_machine_code = explode(',', $c_machine)[0];

        $shift_machine = '';

        foreach ($data as $key_data => $d_acc) {
            $dataMaterial = DB::connection('mysql3')
                ->table('mastermaterial')
                ->where('Code', $d_acc->code_item)
                ->select('Name')
                ->get();

            $dataMaterial = $dataMaterial->map(function ($item) {
                return (array) $item;
            })->toArray();

            $d_acc->material_name = $dataMaterial[0]['Name'] ?? null;

            // $stts = PlannedProductionTmp::where('id_pp', $d_acc->id)->exists();
            // if ($stts) {
            //     $d_acc->status_plan = "ada";
            // } else {
            //     $d_acc->status_plan = "tidak";
            // }
            // $status_plan = PlannedProductionTmp::where('id_pp', $d_acc)
        }


        // dd($data);


        return response()->json(['data' => $data, 'shift' => $shift_machine]);
    }

    public function getDataFirstPlansData($code)
    {
        // dd($code);
        $data = PlanFirstProduction::where('code_plan', $code)->get();
        $c_machine = $data[0]->code_machine;

        $first_machine_code = explode(',', $c_machine)[0];

        $shift_machine = '';

        foreach ($data as $key_data => $d_acc) {
            $dataMaterial = DB::connection('mysql3')
                ->table('mastermaterial')
                ->where('Code', $d_acc->code_item)
                ->select('Name')
                ->get();

            $dataMaterial = $dataMaterial->map(function ($item) {
                return (array) $item;
            })->toArray();

            $d_acc->material_name = $dataMaterial[0]['Name'] ?? null;

            // $stts = PlannedProductionTmp::where('id_pp', $d_acc->id)->exists();
            // if ($stts) {
            //     $d_acc->status_plan = "ada";
            // } else {
            //     $d_acc->status_plan = "tidak";
            // }
            // $status_plan = PlannedProductionTmp::where('id_pp', $d_acc)
        }


        // dd($data);


        return response()->json(['data' => $data]);
    }

    public function savePlanMingguanData(Request $request)
    {
        // dd($request->all());
        try {
            $data = json_decode($request->input('data'), true);
            // dd($data);

            if (!$data) {
                return response()->json(['error' => 'Invalid data format'], 400);
            }

            $codePlan = $data['code_plan'];

            // Save main table data
            if (isset($data['main_table']) && !empty($data['main_table'])) {
                $this->saveMainTableData($codePlan, $data['main_table']);
            }

            // Save pelumasan table data
            // if (isset($data['pelumasan_table']) && !empty($data['pelumasan_table'])) {
            //     $this->savePelumasanTableData($codePlan, $data['pelumasan_table']);
            // }

            // Save machine tables data
            if (isset($data['machine_tables']) && !empty($data['machine_tables'])) {
                foreach ($data['machine_tables'] as $machine => $tableData) {
                    if (!empty($tableData)) {
                        $this->saveMachineTableData($codePlan, $machine, $tableData);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Data saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function saveMainTableData($codePlan, $tableData)
    {
        foreach ($tableData as $row) {
            if (empty($row[0])) continue;

            if (!empty($row[15])) {
                PlanFirstProduction::where('id', $row[15])->update(['status_proses' => 'MASUK PLAN HARIAN']);
            } else {
                return response()->json(['error' => 'Invalid data format'], 400);
            }
        }
    }

    private function savePelumasanTableData($codePlan, $tableData)
    {
        // First delete existing data for this plan
        PelumasanMaintenance::where('code_plan', $codePlan)->delete();

        foreach ($tableData as $row) {
            if (empty($row[0])) continue; // Skip empty rows

            PelumasanMaintenance::create([
                'code_plan' => $codePlan,
                'kode_prob' => $row[0] ?? '',
                'mesin' => $row[1] ?? '',
                'est_hours' => $row[4] ?? 0,
                'est_day' => $row[5] ?? 0,
                'start_time' => $row[6] ?? '',
                'setup' => $row[7] ?? 0,
                'istirahat' => $row[8] ?? 0,
                'end_time' => $row[9] ?? '',
                'status' => 'active',
            ]);
        }
    }

    private function saveMachineTableData($codePlan, $machine, $tableData)
    {
        foreach ($tableData as $row) {
            if (empty($row[0])) continue; // Skip empty rows

            $process = PlanFirstProduction::where('code_plan', $codePlan)->first();

            PlanContinuedProduction::create([
                'id_first_plan' => $row[15] ?? null,
                'code_plan' => $codePlan,
                'code_item' => $row[0] ?? '',
                'code_machine' => $machine,
                'material_name' => $row[1] ?? '',
                'quantity' => $row[2] ?? 0,
                'up_cetak' => $row[11] ?? 0,
                'capacity' => $row[3] ?? 0,
                'est_jam' => $row[4] ?? 0,
                'est_day' => $row[5] ?? 0,
                'start_jam' => $row[6] ?? '',
                'end_jam' => $row[9] ?? '',
                'setup' => $row[7] ?? 0,
                'istirahat' => $row[8] ?? 0,
                'flag_status' => 'DAY PLAN',
                'wo_docno' => $row[12] ?? '',
                'so_docno' => $row[13] ?? '',
                'delivery_date' => $row[10] ?? '',
                'shift_msn' => $row[14] ?? '',
                'status_proses' => 'PLAN HARIAN',
                'process' => $process->process ?? '',
                'department' => $process->department ?? '',
                'created_by' => Auth::user()->name,
                'changed_by' => Auth::user()->name,
                'deleted_by' => Auth::user()->name,
                'created_at' => now(),
                'updated_at' => now(),
                'date_plan' => !empty($row[6]) ? date('Y-m-d', strtotime($row[6])) : '',
            ]);
        }
    }

    public function viewPlanFirstProduction()
    {
        return view('main.process.plan-first-production');
    }

    public function getPlanFirstData(Request $request)
    {
        $query = PlanFirstProduction::with('machine')
            ->whereNotNull('start_jam')
            ->whereNotNull('end_jam')
            ->where('flag_status', '!=', 'FINISH');

        // Filter berdasarkan tanggal start_jam dan end_jam jika parameter tanggal diberikan
        if ($request->has('date_from') && $request->has('date_to')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            Log::info('Filtering plan data by date range:', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);

            // Filter: data yang start_jam atau end_jam berada dalam range tanggal yang dipilih
            // Atau range tanggal overlap dengan range start_jam dan end_jam
            $query->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween(DB::raw('DATE(start_jam)'), [$dateFrom, $dateTo])
                    ->orWhereBetween(DB::raw('DATE(end_jam)'), [$dateFrom, $dateTo])
                    ->orWhere(function ($subQ) use ($dateFrom, $dateTo) {
                        // Include jika range tanggal overlap dengan range start_jam dan end_jam
                        $subQ->where(DB::raw('DATE(start_jam)'), '<=', $dateTo)
                            ->where(DB::raw('DATE(end_jam)'), '>=', $dateFrom);
                    });
            });
        }

        $data = $query->orderBy('start_jam', 'asc')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'code_plan' => $plan->code_plan,
                    'code_machine' => $plan->code_machine,
                    'code_item' => $plan->code_item,
                    'name_item' => DB::connection('mysql3')
                        ->table('mastermaterial')
                        ->where('Code', $plan->code_item)
                        ->select('Name')
                        ->first()
                        ->Name ?? '',
                    'wo_docno' => $plan->wo_docno,
                    'so_docno' => $plan->so_docno,
                    'quantity' => $plan->quantity,
                    'start_jam' => $plan->start_jam ? date('Y-m-d\TH:i:s', strtotime($plan->start_jam)) : null,
                    'end_jam' => $plan->end_jam ? date('Y-m-d\TH:i:s', strtotime($plan->end_jam)) : null,
                    'est_jam' => $plan->est_jam,
                    'flag_status' => $plan->flag_status,
                    'process' => $plan->process,
                    'delivery_date' => $plan->delivery_date,
                    'material_name' => $plan->material_name,
                    'capacity' => $plan->capacity,
                    'up_cetak' => $plan->up_cetak,
                    'setup' => $plan->setup,
                    'istirahat' => $plan->istirahat,
                    'catatan' => $plan->catatan_proses,
                    'status_item' => $plan->status_item,
                    'keterangan_item' => $plan->keterangan_item,
                    'job_order' => $plan->job_order,
                    'updated_at' => $plan->updated_at,
                    'plate_prepress' => $plan->plate_prepress,
                    'prepress_updated_at' => $plan->prepress_updated_at,
                    'prepress_updated_by' => $plan->prepress_updated_by,
                ];
            });

        Log::info('Plan First Data:', ['data' => $data->toArray()]);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getPlanPlongData(Request $request)
    {
        $query = PlanFirstProduction::with('machine')
            ->whereNotNull('start_jam')
            ->whereNotNull('end_jam')
            ->where('flag_status', '!=', 'FINISH')
            ->where(function ($q) {
                $q->where('process', 'PLG')
                    ->orWhere('process', 'PLONG')
                    ->orWhere('process', 'LIKE', '%PLG%')
                    ->orWhere('process', 'LIKE', '%PLONG%');
            });

        // Filter berdasarkan tanggal start_jam dan end_jam jika parameter tanggal diberikan
        if ($request->has('date_from') && $request->has('date_to')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            Log::info('Filtering PLONG plan data by date range:', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);

            // Filter: data yang start_jam atau end_jam berada dalam range tanggal yang dipilih
            // Atau range tanggal overlap dengan range start_jam dan end_jam
            // Untuk PLONG: lebih fleksibel karena item duplikat bisa memiliki start_jam yang berbeda
            $query->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween(DB::raw('DATE(start_jam)'), [$dateFrom, $dateTo])
                    ->orWhereBetween(DB::raw('DATE(end_jam)'), [$dateFrom, $dateTo])
                    ->orWhere(function ($subQ) use ($dateFrom, $dateTo) {
                        // Include jika range tanggal overlap dengan range start_jam dan end_jam
                        $subQ->where(DB::raw('DATE(start_jam)'), '<=', $dateTo)
                            ->where(DB::raw('DATE(end_jam)'), '>=', $dateFrom);
                    });
            });
        }

        $data = $query->orderBy('start_jam', 'asc')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'code_plan' => $plan->code_plan,
                    'code_machine' => $plan->code_machine,
                    'code_item' => $plan->code_item,
                    'code_item_bom' => $plan->code_item_bom, // Material Code BOM untuk PLONG
                    'name_item' => DB::connection('mysql3')
                        ->table('mastermaterial')
                        ->where('Code', $plan->code_item)
                        ->select('Name')
                        ->first()
                        ->Name ?? '',
                    'wo_docno' => $plan->wo_docno,
                    'so_docno' => $plan->so_docno,
                    'quantity' => $plan->quantity,
                    'start_jam' => $plan->start_jam ? date('Y-m-d\TH:i:s', strtotime($plan->start_jam)) : null,
                    'end_jam' => $plan->end_jam ? date('Y-m-d\TH:i:s', strtotime($plan->end_jam)) : null,
                    'est_jam' => $plan->est_jam,
                    'flag_status' => $plan->flag_status,
                    'process' => $plan->process,
                    'delivery_date' => $plan->delivery_date,
                    'material_name' => $plan->material_name,
                    'capacity' => $plan->capacity,
                    'up_cetak' => $plan->up_cetak,
                    'setup' => $plan->setup,
                    'istirahat' => $plan->istirahat,
                    'catatan' => $plan->catatan_proses,
                ];
            });

        // Debug: Cek item dengan code_item dan wo_docno yang sama untuk PLONG
        $duplicateItems = $data->filter(function ($item) {
            return $item['code_item'] === 'DS.0190.0260' && $item['wo_docno'] === 'WOT-250613-0003';
        });

        Log::info('Plan PLONG Data:', [
            'count' => $data->count(),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'duplicate_items_count' => $duplicateItems->count(),
            'duplicate_items' => $duplicateItems->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'code_item' => $item['code_item'],
                    'wo_docno' => $item['wo_docno'],
                    'code_item_bom' => $item['code_item_bom'],
                    'code_machine' => $item['code_machine']
                ];
            })->toArray()
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getPlanGlueingData(Request $request)
    {
        $query = PlanFirstProduction::with('machine')
            ->whereNotNull('start_jam')
            ->whereNotNull('end_jam')
            ->where('flag_status', '!=', 'FINISH')
            ->where(function ($q) {
                $q->where('process', 'LEM')
                    ->orWhere('process', 'GLUEING')
                    ->orWhere('process', 'GLU')
                    ->orWhere('process', 'LIKE', '%LEM%')
                    ->orWhere('process', 'LIKE', '%GLUEING%')
                    ->orWhere('process', 'LIKE', '%GLU%');
            });

        // Filter berdasarkan tanggal start_jam dan end_jam jika parameter tanggal diberikan
        if ($request->has('date_from') && $request->has('date_to')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            Log::info('Filtering GLUEING plan data by date range:', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]);

            // Filter: data yang start_jam atau end_jam berada dalam range tanggal yang dipilih
            // Atau range tanggal overlap dengan range start_jam dan end_jam
            $query->where(function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween(DB::raw('DATE(start_jam)'), [$dateFrom, $dateTo])
                    ->orWhereBetween(DB::raw('DATE(end_jam)'), [$dateFrom, $dateTo])
                    ->orWhere(function ($subQ) use ($dateFrom, $dateTo) {
                        // Include jika range tanggal overlap dengan range start_jam dan end_jam
                        $subQ->where(DB::raw('DATE(start_jam)'), '<=', $dateTo)
                            ->where(DB::raw('DATE(end_jam)'), '>=', $dateFrom);
                    });
            });
        }

        $data = $query->orderBy('start_jam', 'asc')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'code_plan' => $plan->code_plan,
                    'code_machine' => $plan->code_machine,
                    'code_item' => $plan->code_item,
                    'name_item' => DB::connection('mysql3')
                        ->table('mastermaterial')
                        ->where('Code', $plan->code_item)
                        ->select('Name')
                        ->first()
                        ->Name ?? '',
                    'wo_docno' => $plan->wo_docno,
                    'so_docno' => $plan->so_docno,
                    'quantity' => $plan->quantity,
                    'start_jam' => $plan->start_jam ? date('Y-m-d\TH:i:s', strtotime($plan->start_jam)) : null,
                    'end_jam' => $plan->end_jam ? date('Y-m-d\TH:i:s', strtotime($plan->end_jam)) : null,
                    'est_jam' => $plan->est_jam,
                    'flag_status' => $plan->flag_status,
                    'process' => $plan->process,
                    'delivery_date' => $plan->delivery_date,
                    'material_name' => $plan->material_name,
                    'capacity' => $plan->capacity,
                    'up_cetak' => $plan->up_cetak,
                    'setup' => $plan->setup,
                    'istirahat' => $plan->istirahat,
                    'catatan' => $plan->catatan_proses,
                    'status_item' => $plan->status_item,
                    'keterangan_item' => $plan->keterangan_item,
                    'job_order' => $plan->job_order,
                    'updated_at' => $plan->updated_at,
                ];
            });

        Log::info('Plan GLUEING Data:', [
            'count' => $data->count(),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to')
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getMachineShiftConfig()
    {
        $configs = \App\Models\MachineShift::getAllShifts();
        return response()->json([
            'status' => 'success',
            'data' => $configs
        ]);
    }

    public function saveMachineShiftConfig(Request $request)
    {
        $request->validate([
            'machine' => 'required|string',
            'shift' => 'required|integer|in:2,3'
        ]);

        \App\Models\MachineShift::setShift($request->machine, $request->shift);

        return response()->json([
            'status' => 'success',
            'message' => 'Shift configuration saved successfully'
        ]);
    }

    public function exportPlanFirst()
    {
        $plans = PlanFirstProduction::with(['machine', 'material'])
            ->orderBy('code_machine')
            ->orderBy('start_jam')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Code Plan');
        $sheet->setCellValue('B1', 'Machine');
        $sheet->setCellValue('C1', 'Material Code');
        $sheet->setCellValue('D1', 'Material Name');
        $sheet->setCellValue('E1', 'WO DocNo');
        $sheet->setCellValue('F1', 'SO DocNo');
        $sheet->setCellValue('G1', 'Quantity');
        $sheet->setCellValue('H1', 'Start Time');
        $sheet->setCellValue('I1', 'End Time');
        $sheet->setCellValue('J1', 'Est. Hours');
        $sheet->setCellValue('K1', 'Status');

        $row = 2;
        foreach ($plans as $plan) {
            $sheet->setCellValue('A' . $row, $plan->code_plan);
            $sheet->setCellValue('B' . $row, $plan->machine->Description ?? '');
            $sheet->setCellValue('C' . $row, $plan->code_item);
            $sheet->setCellValue('D' . $row, $plan->material->MaterialName ?? '');
            $sheet->setCellValue('E' . $row, $plan->wo_docno);
            $sheet->setCellValue('F' . $row, $plan->so_docno);
            $sheet->setCellValue('G' . $row, $plan->quantity);
            $sheet->setCellValue('H' . $row, $plan->start_jam);
            $sheet->setCellValue('I' . $row, $plan->end_jam);
            $sheet->setCellValue('J' . $row, $plan->est_jam);
            $sheet->setCellValue('K' . $row, $this->getStatusText($plan->flag_status));
            $row++;
        }

        // Auto size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'plan_first_production_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Pending';
            case 1:
                return 'In Progress';
            case 2:
                return 'Completed';
            default:
                return 'Unknown';
        }
    }

    public function getMachineData()
    {
        $machines = Machine::where('Description', 'not like', '%JANGAN DIPAKAI%')
            ->orderBy('Description')
            ->get();

        Log::info('Machine Data:', ['data' => $machines->toArray()]);

        return response()->json([
            'status' => 'success',
            'data' => $machines
        ]);
    }

    public function getMachineDataCetak()
    {
        $machines = Machine::where('Description', 'not like', '%JANGAN DIPAKAI%')
            // ->where('Department', 'like', '%CTK%')
            ->orderBy('Description')
            ->get();

        Log::info('Machine Data:', ['data' => $machines->toArray()]);

        return response()->json([
            'status' => 'success',
            'data' => $machines
        ]);
    }

    public function getDepartments()
    {
        $departments = Machine::query()
            ->select('Department')
            ->whereNotNull('Department')
            ->where('Department', '!=', '')
            ->distinct()
            ->orderBy('Department')
            ->pluck('Department');
        return response()->json(['departments' => $departments]);
    }

    /**
     * Generate array processes untuk item, dengan filtering jika ada userChoice.
     * Jika $ptgTumOnly true, hanya ambil proses PTG/TUM saja.
     */
    private function generateProcesses($item, $userChoice = null, $ptgTumOnly = false)
    {
        $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
            ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
            ->get();
        // Khusus untuk WOP, quantity wajib 0 PCS
        $quantity = $item['Quantity'];
        if (isset($item['WODocNo']) && str_starts_with($item['WODocNo'], 'WOP')) {
            $quantity = 0;
        } elseif ($item['Quantity'] <= 500) {
            $quantity = 500;
        }
        $prosesData = [];
        foreach ($masterBomHeader as $bom) {
            $formula = $bom->Formula;
            $proses = '';
            $acuan = '';
            $mesin = '';
            $estimation = 0;
            // Mapping proses, acuan, mesin, estimasi (copy dari logic utama Anda)
            if (strpos($formula, '.CTK.') !== false) {
                $proses = 'CTK';
                $acuan = 'Cetak';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_ctk . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_ctk . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    // Khusus untuk WOP, estimation wajib 8 jam
                    if (isset($item['WODocNo']) && str_starts_with($item['WODocNo'], 'WOP')) {
                        $estimation = 8; // 8 jam untuk WOP
                    } else {
                        $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                    }
                }
            } elseif (strpos($formula, '.EPL.') !== false) {
                $proses = 'EPL';
                $acuan = 'Emboss / Plong';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_epl . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_epl . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    // Khusus untuk WOP, estimation wajib 8 jam
                    if (isset($item['WODocNo']) && str_starts_with($item['WODocNo'], 'WOP')) {
                        $estimation = 8; // 8 jam untuk WOP
                    } else {
                        $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                    }
                }
            } elseif (strpos($formula, '.EMB.') !== false) {
                $proses = 'EMB';
                $acuan = 'Emboss';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_emb . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_emb . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    // Khusus untuk WOP, estimation wajib 8 jam
                    if (isset($item['WODocNo']) && str_starts_with($item['WODocNo'], 'WOP')) {
                        $estimation = 8; // 8 jam untuk WOP
                    } else {
                        $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                    }
                }
            } elseif (strpos($formula, '.HP.') !== false) {
                $proses = 'HP';
                $acuan = 'Hot Print';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_hp . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_hp . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.KPS.') !== false) {
                $proses = 'KPS';
                $acuan = 'Kupas';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_kps . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_kps . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.STR.') !== false) {
                $proses = 'STR';
                $acuan = 'Sortir';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_str . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_str . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.UV.') !== false) {
                $proses = 'UV';
                $acuan = 'UV';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_uv . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_uv . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.STU.') !== false) {
                $proses = 'STU';
                $acuan = 'Sortir Ulang';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_stu . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_stu . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.PLG.') !== false) {
                $proses = 'PLG';
                $acuan = 'Plong';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_plg . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_plg . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.PTG.') !== false) {
                $proses = 'PTG';
                $acuan = 'Potong';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_ptg . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_ptg . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.TUM.') !== false) {
                $proses = 'TUM';
                $acuan = 'Tumpuk';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_tum . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_tum . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } else {
                $proses = 'LEM';
                $acuan = 'Lem';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = Machine::where('Code', 'like', '%' . $machineProses->m_lem . '%')->first();
                    // $detailMachine = DB::connection('mysql3')
                    //     ->table('mastermachine')
                    //     ->where('Code', 'like', '%' . $machineProses->m_lem . '%')
                    //     ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            }
            // Filter jika hanya ingin PTG/TUM
            if ($ptgTumOnly && !in_array($proses, ['PTG', 'TUM'])) {
                continue;
            }
            $prosesData[] = [
                'proses' => $proses,
                'acuan' => $acuan,
                'formula' => $formula,
                'material_code' => $bom->MaterialCode,
                'machine' => $mesin,
                'estimation' => $estimation,
                'SODocNo' => $item['SODocNo'] ?? null,
                'WODocNo' => $item['WODocNo'] ?? null,
                'MaterialName' => $item['MaterialName'] ?? null,
                'Quantity' => $item['Quantity'] ?? null
            ];
        }
        // Filter jika ada userChoice
        if ($userChoice === 'ptg_only') {
            $prosesData = array_filter($prosesData, fn($p) => $p['proses'] !== 'TUM');
        } elseif ($userChoice === 'tum_only') {
            $prosesData = array_filter($prosesData, fn($p) => $p['proses'] !== 'PTG');
        }
        return array_values($prosesData);
    }

    /**
     * Filter hasil akhir proses agar hanya ada satu PTG dan satu TUM.
     * @param array $prosesArray
     * @return array
     */
    private function filterSinglePTGandTUM($prosesArray)
    {
        $result = [];
        $ptgAdded = false;
        $tumAdded = false;
        foreach ($prosesArray as $p) {
            if (($p['Proses'] ?? $p['proses']) === 'PTG') {
                if (!$ptgAdded) {
                    $result[] = $p;
                    $ptgAdded = true;
                }
            } elseif (($p['Proses'] ?? $p['proses']) === 'TUM') {
                if (!$tumAdded) {
                    $result[] = $p;
                    $tumAdded = true;
                }
            } else {
                $result[] = $p;
            }
        }
        return $result;
    }

    /**
     * Filter hasil akhir proses agar hanya ada satu proses per jenis (misal CTK, EMB, PLG, dst).
     * @param array $prosesArray
     * @return array
     */
    private function filterUniqueProcesses($prosesArray)
    {
        $seen = [];
        $result = [];
        foreach ($prosesArray as $p) {
            $key = ($p['Proses'] ?? $p['proses']);
            if (!isset($seen[$key])) {
                $result[] = $p;
                $seen[$key] = true;
            }
        }
        return $result;
    }

    /**
     * Optimasi penempatan proses CTK berdasarkan kesamaan warna
     * sambil tetap mempertahankan urutan logis proses
     */
    private function optimizeCTKPlacement($planPerItem, $startDate)
    {
        // Pisahkan proses CTK dari semua item
        $ctkProcesses = [];
        $nonCtkProcesses = [];

        foreach ($planPerItem as $uniqueKey => $plans) {
            foreach ($plans as $plan) {
                if ($plan['Proses'] === 'CTK') {
                    $ctkProcesses[] = [
                        'materialCode' => $uniqueKey,
                        'plan' => $plan,
                        'warna' => $this->getWarnaForMaterial($plan['MaterialCode'])
                    ];
                } else {
                    $nonCtkProcesses[] = [
                        'materialCode' => $uniqueKey,
                        'plan' => $plan
                    ];
                }
            }
        }

        // Optimasi urutan CTK berdasarkan kesamaan warna
        if (count($ctkProcesses) > 1) {
            $ctkProcesses = $this->optimizeCTKOrder($ctkProcesses);
        }

        // Reintegrasi CTK ke dalam urutan proses yang sudah ada
        return $this->reintegrateCTKProcesses($ctkProcesses, $nonCtkProcesses, $startDate);
    }

    /**
     * Ambil data warna untuk material tertentu
     */
    private function getWarnaForMaterial($materialCode)
    {
        $warnaDetail = MappingItem::where('kode', $materialCode)->first();
        if (!$warnaDetail) return [];

        $warnaTerpilih = [];
        for ($i = 1; $i <= 15; $i++) {
            $key = 't' . $i;
            if (isset($warnaDetail->$key) && !empty($warnaDetail->$key)) {
                $warnaTerpilih[$key] = $warnaDetail->$key;
            }
        }
        return $warnaTerpilih;
    }

    /**
     * Optimasi urutan CTK menggunakan algoritma yang lebih canggih
     */
    private function optimizeCTKOrder($ctkProcesses)
    {
        if (count($ctkProcesses) <= 1) return $ctkProcesses;

        // Gunakan algoritma yang lebih sophisticated
        $result = [$ctkProcesses[0]];
        $remaining = array_slice($ctkProcesses, 1);

        while (!empty($remaining)) {
            $current = end($result);
            $bestMatch = null;
            $bestScore = -1;
            $bestIndex = -1;

            foreach ($remaining as $index => $candidate) {
                $score = $this->calculateColorSimilarity($current['warna'], $candidate['warna']);

                // Tambahkan faktor lain seperti ukuran material, prioritas delivery
                $sizeBonus = $this->calculateSizeBonus($current['plan'], $candidate['plan']);
                $deliveryBonus = $this->calculateDeliveryBonus($current['plan'], $candidate['plan']);

                $totalScore = $score + $sizeBonus + $deliveryBonus;

                if ($totalScore > $bestScore) {
                    $bestScore = $totalScore;
                    $bestMatch = $candidate;
                    $bestIndex = $index;
                }
            }

            if ($bestMatch) {
                $result[] = $bestMatch;
                array_splice($remaining, $bestIndex, 1);
            } else {
                $result[] = $remaining[0];
                array_shift($remaining);
            }
        }

        return $result;
    }

    /**
     * Hitung kesamaan warna dengan bobot yang lebih sophisticated
     */
    private function calculateColorSimilarity($warna1, $warna2)
    {
        if (empty($warna1) || empty($warna2)) return 0;

        $commonColors = 0;
        $totalColors = 0;

        foreach ($warna1 as $key => $color1) {
            if (!empty($color1)) {
                $totalColors++;
                if (isset($warna2[$key]) && $color1 === $warna2[$key]) {
                    $commonColors++;
                }
            }
        }

        // Berikan bonus untuk kesamaan warna yang lebih banyak
        $similarityRatio = $totalColors > 0 ? $commonColors / $totalColors : 0;
        return $similarityRatio * 10; // Skala 0-10
    }

    /**
     * Hitung bonus berdasarkan ukuran material (material dengan ukuran serupa lebih efisien)
     */
    private function calculateSizeBonus($plan1, $plan2)
    {
        $qty1 = $plan1['Quantity'] ?? 0;
        $qty2 = $plan2['Quantity'] ?? 0;

        if ($qty1 == 0 || $qty2 == 0) return 0;

        $ratio = min($qty1, $qty2) / max($qty1, $qty2);
        return $ratio * 2; // Bonus maksimal 2 poin
    }

    /**
     * Hitung bonus berdasarkan prioritas delivery date
     */
    private function calculateDeliveryBonus($plan1, $plan2)
    {
        // Implementasi berdasarkan delivery date jika tersedia
        return 0; // Placeholder
    }

    /**
     * Reintegrasi proses CTK yang sudah dioptimasi ke dalam urutan proses
     */
    private function reintegrateCTKProcesses($ctkProcesses, $nonCtkProcesses, $startDate)
    {
        $result = [];
        $currentTime = Carbon::parse($startDate)->setTime(8, 0, 0);

        // Jadwalkan CTK terlebih dahulu dengan urutan yang sudah dioptimasi
        foreach ($ctkProcesses as $ctkProcess) {
            $materialCode = $ctkProcess['materialCode'];
            $plan = $ctkProcess['plan'];

            // Hitung estimasi waktu
            $machineData = Machine::where('Code', $plan['Machine'])->first();
            // $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
            $capacityPerHour = $machineData && isset($machineData->CapacityPerHour) ? $machineData->CapacityPerHour : 1;
            $quantity = $plan['Quantity'];
            $estimation = $quantity / $capacityPerHour;
            $estSeconds = $estimation * 3600;

            $start = clone $currentTime;
            $end = (clone $start)->addSeconds($estSeconds);

            // Pastikan quantity minimal 500 dan hilangkan desimal
            $finalQuantity = max(500, (int)$quantity);
            // dd($finalQuantity);

            $result[$materialCode][] = [
                'MaterialCode' => $plan['MaterialCode'],
                'MaterialName' => $plan['MaterialName'],
                'CodeItem' => $plan['CodeItem'],
                'Machine' => $plan['Machine'],
                'Proses' => 'CTK',
                'StartJam' => $start->format('Y-m-d H:i:s'),
                'EndJam' => $end->format('Y-m-d H:i:s'),
                'SetupSeconds' => 0,
                'BreakSeconds' => 0,
                'Estimation' => $estimation,
                'Quantity' => $finalQuantity,
                'Formula' => $plan['Formula'],
                'SODocNo' => $plan['SODocNo'] ?? null,
                'WODocNo' => $plan['WODocNo'] ?? null,
                'Up' => $plan['Up'],
                'Unit' => $plan['Unit'],
            ];

            $currentTime = clone $end;
        }

        // Tambahkan proses non-CTK setelah CTK selesai
        foreach ($nonCtkProcesses as $nonCtkProcess) {
            $materialCode = $nonCtkProcess['materialCode'];
            $plan = $nonCtkProcess['plan'];

            $machineData = Machine::where('Code', $plan['Machine'])->first();
            // $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
            // $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
            $capacityPerHour = $machineData && isset($machineData->CapacityPerHour) ? $machineData->CapacityPerHour : 1;
            $unitMachine = $machineData && isset($machineData->Unit) ? $machineData->Unit : 'PCS';
            $quantity = $plan['Quantity'];

            // Jika unit mesin dan unit plan berbeda, bagi quantity dengan Up
            if ($unitMachine !== $plan['Unit']) {
                $quantity = $quantity / $plan['Up'];
            }

            $estimation = $quantity / $capacityPerHour;
            $estSeconds = $estimation * 3600;

            // dd($unitMachine);

            $start = clone $currentTime;
            $end = (clone $start)->addSeconds($estSeconds);

            // Pastikan quantity minimal 500 dan hilangkan desimal
            $finalQuantity = max(500, (int)$quantity);

            $result[$materialCode][] = [
                'MaterialCode' => $plan['MaterialCode'],
                'MaterialName' => $plan['MaterialName'],
                'CodeItem' => $plan['CodeItem'],
                'Machine' => $plan['Machine'],
                'Proses' => $plan['Proses'],
                'StartJam' => $start->format('Y-m-d H:i:s'),
                'EndJam' => $end->format('Y-m-d H:i:s'),
                'SetupSeconds' => 0,
                'BreakSeconds' => 0,
                'Estimation' => $estimation,
                'Quantity' => $finalQuantity,
                'Formula' => $plan['Formula'],
                'SODocNo' => $plan['SODocNo'] ?? null,
                'WODocNo' => $plan['WODocNo'] ?? null,
                'Up' => $plan['Up'],
                'Unit' => $plan['Unit'],
            ];

            $currentTime = clone $end;
        }

        return $result;
    }

    /**
     * Kelompokkan plan per item berdasarkan proses dan mesin,
     * kemudian hitung ulang start_jam_new dan end_jam_new
     */
    private function groupAndRecalculateMachineSchedule($planPerItem, $startDate)
    {
        // dd('planPerItem group', $planPerItem);
        // Kelompokkan berdasarkan proses dan mesin
        $groupedByMachine = [];

        foreach ($planPerItem as $uniqueKey => $plans) {
            foreach ($plans as $plan) {
                $key = $plan['Proses'];
                // dd($key);

                if (!isset($groupedByMachine[$key])) {
                    $groupedByMachine[$key] = [];
                }

                $groupedByMachine[$key][] = [
                    'materialCode' => $uniqueKey,
                    'plan' => $plan
                ];
            }
        }

        // dd('groupedByMachine', $groupedByMachine);

        // Urutkan setiap grup berdasarkan start_jam asli
        foreach ($groupedByMachine as $key => $group) {
            usort($group, function ($a, $b) {
                return strtotime($a['plan']['StartJam']) <=> strtotime($b['plan']['StartJam']);
            });
        }

        // dd('groupedByMachine', $groupedByMachine);

        // Hitung ulang start_jam_new dan end_jam_new untuk setiap grup
        $result = [];
        foreach ($groupedByMachine as $key => $group) {
            $currentStart = Carbon::parse($startDate)->setTime(8, 0, 0);
            $machineGroup = []; // Temporary array untuk grup mesin ini

            // dd($group);

            foreach ($group as $item) {

                // dd($item);
                $materialCode = $item['materialCode'];
                $plan = $item['plan'];
                // $codeItem = $item['plan']['CodeItem'];
                // dd($plan);

                // Hitung estimasi waktu berdasarkan kapasitas mesin
                $machineData = Machine::where('Code', $plan['Machine'])->first();
                // $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
                $capacityPerHour = $machineData && isset($machineData->CapacityPerHour) ? $machineData->CapacityPerHour : 1;
                $unitMachine = $machineData && isset($machineData->Unit) ? $machineData->Unit : 'PCS';
                $quantity = $plan['Quantity'];

                // dd($unitMachine, $plan['Unit'], $plan['Up'], $quantity);

                // Jika unit mesin dan unit plan berbeda, bagi quantity dengan Up
                if ($unitMachine !== $plan['Unit']) {
                    $quantity = $quantity / $plan['Up'];
                }

                // dd($quantity);

                $estimation = $quantity / $capacityPerHour;
                $estSeconds = $estimation * 3600;

                // Tambahkan setup time jika berbeda material dari sebelumnya
                $setupSeconds = 0;
                if (
                    !empty($machineGroup) &&
                    end($machineGroup)['MaterialCode'] !== $materialCode
                ) {
                    $setupSeconds = 3600; // 1 jam setup
                }

                $start = clone $currentStart;
                $end = (clone $start)->addSeconds($estSeconds + $setupSeconds);

                // Tambahkan break time jika melewati jam istirahat
                $breakSeconds = $this->calculateBreakTime($start, $end);
                $end->addSeconds($breakSeconds);

                // FIX: Gunakan uniqueKey (MaterialCode_WODocNo) sebagai key, bukan materialCode saja
                if (!isset($result[$materialCode])) {
                    $result[$materialCode] = [];
                }

                // Pastikan quantity minimal 500 dan hilangkan desimal
                $finalQuantity = max(500, (int)$quantity);

                // FIX: Gunakan uniqueKey (MaterialCode_WODocNo) sebagai key, bukan materialCode saja
                $result[$materialCode][] = [
                    'MaterialCode' => $materialCode,
                    'MaterialName' => $plan['MaterialName'],
                    'CodeItem' => $plan['CodeItem'] ?? null,
                    'Machine' => $plan['Machine'],
                    'Proses' => $plan['Proses'],
                    'StartJam' => $plan['StartJam'], // Waktu asli
                    'EndJam' => $plan['EndJam'], // Waktu asli
                    'StartJamNew' => $start->format('Y-m-d H:i:s'), // Waktu baru setelah dikelompokkan
                    'EndJamNew' => $end->format('Y-m-d H:i:s'), // Waktu baru setelah dikelompokkan
                    'SetupSeconds' => $setupSeconds,
                    'BreakSeconds' => $breakSeconds,
                    'Estimation' => $estimation,
                    'Quantity' => $finalQuantity,
                    'Formula' => $plan['Formula'],
                    'SODocNo' => $plan['SODocNo'] ?? null,
                    'WODocNo' => $plan['WODocNo'] ?? null,
                    'Up' => $plan['Up'],
                    'Unit' => $plan['Unit'],
                ];

                $machineGroup[] = [
                    'MaterialCode' => $materialCode,
                    'MaterialName' => $plan['MaterialName'],
                    'Machine' => $plan['Machine'],
                    'CodeItem' => $plan['CodeItem'] ?? null,
                    'Proses' => $plan['Proses'],
                    'StartJam' => $plan['StartJam'], // Waktu asli
                    'EndJam' => $plan['EndJam'], // Waktu asli
                    'StartJamNew' => $start->format('Y-m-d H:i:s'), // Waktu baru setelah dikelompokkan
                    'EndJamNew' => $end->format('Y-m-d H:i:s'), // Waktu baru setelah dikelompokkan
                    'SetupSeconds' => $setupSeconds,
                    'BreakSeconds' => $breakSeconds,
                    'Estimation' => $estimation,
                    'Quantity' => $quantity,
                    'Formula' => $plan['Formula'],
                    'SODocNo' => $plan['SODocNo'] ?? null,
                    'WODocNo' => $plan['WODocNo'] ?? null,
                    'Up' => $plan['Up'],
                    'Unit' => $plan['Unit'],
                ];

                // dd($machineGroup);

                $currentStart = clone $end;
            }
        }

        return $result;
    }

    /**
     * Hitung break time berdasarkan jam kerja
     */
    private function calculateBreakTime($start, $end)
    {
        $breakSeconds = 0;
        $breaks = [
            ['start' => 12, 'end' => 13], // Istirahat siang
            ['start' => 19, 'end' => 20], // Istirahat malam
            ['start' => 1, 'end' => 3],   // Istirahat dini hari
        ];

        $processStart = clone $start;
        $processEnd = clone $end;

        foreach ($breaks as $break) {
            // Tentukan waktu break di hari proses mulai
            $breakStart = (clone $processStart)->setTime($break['start'], 0, 0);
            $breakEnd = (clone $processStart)->setTime($break['end'], 0, 0);

            if ($breakEnd->lessThan($breakStart)) {
                $breakEnd->addDay();
            }

            // Jika proses melewati jam break
            if ($processStart->lt($breakEnd) && $processEnd->gt($breakStart)) {
                $breakSeconds += 3600; // 1 jam break
            }

            // Jika proses lebih dari 1 hari, cek break di hari berikutnya
            $spanDays = $processStart->diffInDays($processEnd);
            for ($d = 1; $d <= $spanDays; $d++) {
                $breakStartDay = (clone $breakStart)->addDays($d);
                $breakEndDay = (clone $breakEnd)->addDays($d);

                if ($processStart->lt($breakEndDay) && $processEnd->gt($breakStartDay)) {
                    $breakSeconds += 3600; // 1 jam break
                }
            }
        }

        return $breakSeconds;
    }

    /**
     * Tampilkan pengelompokkan berdasarkan proses dan mesin
     */
    private function displayGroupedByMachine($planPerItem)
    {
        $groupedByMachine = [];

        foreach ($planPerItem as $materialCode => $plans) {
            // dd($plans);
            foreach ($plans as $plan) {
                $key = $plan['Proses'];

                if (!isset($groupedByMachine[$key])) {
                    $groupedByMachine[$key] = [];
                }

                // Pastikan quantity minimal 500 dan hilangkan desimal
                $finalQuantity = max(500, (int)($plan['Quantity'] ?? 0));

                $groupedByMachine[$key][] = [
                    'MaterialCode' => $materialCode,
                    'MaterialName' => $plan['MaterialName'],
                    'CodeItem' => $plan['CodeItem'],
                    'Machine' => $plan['Machine'],
                    'Proses' => $plan['Proses'],
                    'StartJam' => $plan['StartJam'],
                    'EndJam' => $plan['EndJam'],
                    'StartJamNew' => $plan['StartJamNew'] ?? $plan['StartJam'],
                    'EndJamNew' => $plan['EndJamNew'] ?? $plan['EndJam'],
                    'SetupSeconds' => $plan['SetupSeconds'] ?? 0,
                    'BreakSeconds' => $plan['BreakSeconds'] ?? 0,
                    'Estimation' => $plan['Estimation'],
                    'Quantity' => $finalQuantity,
                    'Formula' => $plan['Formula'],
                ];
            }
        }

        // Urutkan setiap grup berdasarkan StartJamNew
        foreach ($groupedByMachine as $key => &$group) {
            usort($group, function ($a, $b) {
                return strtotime($a['StartJamNew']) <=> strtotime($b['StartJamNew']);
            });
        }

        // dd('groupedByMachine', $groupedByMachine);

        return $groupedByMachine;
    }

    public function savePlanFromPreview(Request $request)
    {
        // dd($request->all());
        $planData = $request->input('plan_data');

        if (!$planData) {
            return response()->json([
                'success' => false,
                'message' => 'Data plan tidak valid'
            ], 400);
        }

        $code_plan = 'PPIC' . date('YmdHis');
        $finalSchedule = [];
        $machineLastEnd = [];

        // Ambil semua proses yang sudah ada di DB untuk mesin terkait (hanya yang belum selesai)
        $existing = [];
        $machines = [];
        foreach ($planData['planPerItem'] as $uniqueKey => $plans) {
            foreach ($plans as $row) {
                $machines[$row['Machine']] = true;
            }
        }
        $machines = array_keys($machines);
        $existingDb = PlanFirstProduction::whereIn('code_machine', $machines)->orderBy('end_jam', 'asc')->get();
        foreach ($existingDb as $ex) {
            $machineLastEnd[$ex->code_machine] = max(
                isset($machineLastEnd[$ex->code_machine]) ? strtotime($machineLastEnd[$ex->code_machine]) : 0,
                strtotime($ex->end_jam)
            ) ? $ex->end_jam : $ex->end_jam;
        }

        // Proses batch plan
        foreach ($planData['planPerItem'] as $uniqueKey => $plans) {
            foreach ($plans as $row) {
                $machine = $row['Machine'];
                $est = $row['Estimation'];
                $start = $row['StartJamNew'] ?? $row['StartJam'];
                $end = $row['EndJamNew'] ?? $row['EndJam'];

                // Jika ada proses sebelumnya di mesin ini, dan end time-nya >= start, geser
                if (isset($machineLastEnd[$machine]) && strtotime($machineLastEnd[$machine]) > strtotime($start)) {
                    $start = $machineLastEnd[$machine];
                    $end = date('Y-m-d H:i:s', strtotime($start) + ($est * 3600));
                }
                // Update last end untuk mesin ini
                $machineLastEnd[$machine] = $end;

                $finalSchedule[] = [
                    'code_plan' => $code_plan,
                    'code_machine' => $machine,
                    'so_docno' => $row['SODocNo'] ?? null,
                    'wo_docno' => $row['WODocNo'] ?? null,
                    'code_item' => $row['MaterialCode'],
                    'quantity' => $row['Quantity'],
                    'start_jam' => $start,
                    'end_jam' => $end,
                    'est_jam' => $est,
                    'process' => $row['Proses'],
                    'material_name' => $row['MaterialName'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // dd hasil akhir sebelum create
        // dd($finalSchedule);

        // Save Database
        $save = PlanFirstProduction::insert($finalSchedule);
        if ($save) {
            return response()->json([
                'success' => true,
                'message' => 'Plan berhasil disimpan'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Plan gagal disimpan'
            ]);
        }
    }

    public function indexPlanFirstProduction()
    {
        return view('main.process.vis-timeline');
    }

    public function indexPlanFirstTable()
    {
        return view('main.process.timeline-table');
    }

    public function indexPlanFirstTablePlong()
    {
        return view('main.process.timeline-table-plong');
    }

    public function indexPlanFirstTableUppic()
    {
        return view('main.process.timeline-table-uppic');
    }

    public function indexPlanFirstTablePrepress()
    {
        return view('main.process.prepress.timeline-table-prepress');
    }

    public function indexPlanFirstTableGlueing()
    {
        return view('main.process.timeline-table-glueing');
    }

    public function savePlatePrepress(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|integer',
                'plate_prepress' => 'nullable|string|max:255',
            ]);

            $item = PlanFirstProduction::find($request->item_id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ], 404);
            }

            // Disable timestamps untuk mencegah update updated_at
            $item->timestamps = false;
            $item->plate_prepress = $request->plate_prepress;

            // Jika BELUM atau kosong, hapus updated_at dan updated_by
            if (empty($request->plate_prepress) || strtoupper($request->plate_prepress) === 'BELUM') {
                $item->prepress_updated_at = null;
                $item->prepress_updated_by = null;
            } else {
                $item->prepress_updated_at = now();
                $item->prepress_updated_by = Auth::user()->name ?? 'System';
            }

            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'PLAT berhasil disimpan',
                'data' => [
                    'plate_prepress' => $item->plate_prepress,
                    'prepress_updated_at' => $item->prepress_updated_at,
                    'prepress_updated_by' => $item->prepress_updated_by
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan PLAT: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexFullCalendarTimeline()
    {
        return view('main.process.fullcalendar-timeline');
    }

    public function indexFullCalendarTest()
    {
        return view('main.process.fullcalendar-test');
    }

    public function indexDhtmlxScheduler()
    {
        return view('main.process.dhtmlx-scheduler');
    }

    public function indexCustomTimeline()
    {
        return view('main.process.vis-timeline-custom');
    }

    // Helper function untuk membersihkan material code
    private function cleanMaterialCode($materialCode)
    {
        // Handle case where materialCode is "details" and process contains full material code
        if ($materialCode === 'details') {
            return $materialCode;
        }

        // Clean materialCode jika ada prefix yang tidak diinginkan
        $cleanMaterialCode = $materialCode;
        if (strpos($materialCode, 'details.WIP.') === 0) {
            $cleanMaterialCode = str_replace('details.WIP.', '', $materialCode);
        }

        return $cleanMaterialCode;
    }

    // Helper function untuk membersihkan process
    private function cleanProcess($process, $materialCode)
    {
        $cleanProcess = $process;

        // Handle case where materialCode is "details" and process contains full material code
        if ($materialCode === 'details' && !empty($process) && strpos($process, '.WIP.') !== false) {
            // Extract material code from process field
            $parts = explode('.WIP.', $process);
            if (count($parts) >= 2) {
                $extractedProcess = $parts[1];
                // Remove any additional suffixes like .X1, .X
                $extractedProcess = explode('.', $extractedProcess)[0];
                $cleanProcess = $extractedProcess;
            }
        }

        // Extract process dari materialCode jika process kosong
        if (empty($cleanProcess) && strpos($materialCode, '.WIP.') !== false) {
            $parts = explode('.WIP.', $materialCode);
            if (count($parts) >= 2) {
                $extractedProcess = $parts[1];
                // Remove any additional suffixes like .X1
                $extractedProcess = explode('.', $extractedProcess)[0];
                $cleanProcess = $extractedProcess;
            }
        }

        return $cleanProcess;
    }

    // Fungsi untuk mendapatkan BOM headers (masterbomh) - HANYA HEADER
    public function getBomData($materialCode, $process)
    {
        try {
            // Debug: Log input parameters
            Log::info('getBomData called with:', [
                'materialCode' => $materialCode,
                'process' => $process
            ]);

            // Clean materialCode dan process
            $cleanMaterialCode = $this->cleanMaterialCode($materialCode);
            $cleanProcess = $this->cleanProcess($process, $materialCode);

            // Cari BOM berdasarkan MaterialCode.WIP.Process
            $wipMaterialCode = $cleanMaterialCode . '.WIP.' . $cleanProcess;

            // Cari semua BOM header yang cocok
            $availableBoms = DB::connection('mysql3')->table('masterbomh')
                ->where('Formula', 'like', '%' . $wipMaterialCode . '%')
                ->get();

            // Jika tidak ditemukan, cari dengan pattern lain
            if ($availableBoms->count() == 0) {
                $availableBoms = DB::connection('mysql3')->table('masterbomh')
                    ->where('Formula', 'like', '%' . $cleanProcess . '%')
                    ->get();
            }

            if ($availableBoms->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No BOM headers found for material: ' . $cleanMaterialCode . ' with process: ' . $cleanProcess,
                    'searched_codes' => [
                        'wip_material_code' => $wipMaterialCode,
                        'clean_material_code' => $cleanMaterialCode,
                        'clean_process' => $cleanProcess
                    ]
                ]);
            }

            // Format BOM options untuk dropdown
            $bomOptions = $availableBoms->map(function ($bom) {
                return [
                    'value' => $bom->Formula,
                    'label' => $bom->MaterialCode . ' - ' . $bom->Formula,
                    'formula' => $bom->Formula,
                    'material_code' => $bom->MaterialCode
                ];
            });

            // Ambil BOM pertama sebagai default
            $defaultBom = $availableBoms->first();

            return response()->json([
                'success' => true,
                'header' => [
                    'MaterialCode' => $defaultBom->MaterialCode,
                    'Formula' => $defaultBom->Formula,
                    'processes' => $this->getProcessesFromFormula($defaultBom->Formula)
                ],
                'bom_options' => $bomOptions,
                'default_formula' => $defaultBom->Formula,
                'has_multiple_boms' => $availableBoms->count() > 1,
                'total_boms' => $availableBoms->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching BOM headers: ' . $e->getMessage()
            ]);
        }
    }

    public function getBomDetailsByFormula($formula)
    {
        // dd($formula);
        try {
            // Debug: Log input formula
            Log::info('getBomDetailsByFormula called with:', ['formula' => $formula]);

            // Get BOM Details berdasarkan formula yang dipilih
            $bomDetails = DB::connection('mysql3')->table('masterbomd')
                ->where('Formula', $formula)
                ->get();

            // dd($bomDetails);

            $formattedDetails = $bomDetails->map(function ($detail) {
                $materialName = DB::connection('mysql3')->table('mastermaterial')
                    ->where('Code', $detail->MaterialCode)
                    ->first();

                return [
                    'ItemCode' => $detail->MaterialCode ?? '',
                    'ItemName' => $materialName->Name ?? '',
                    'Quantity' => $detail->Qty ?? 0,
                    'Unit' => $detail->Unit ?? '',
                    'Sequence' => 1,
                    'Notes' => '',
                    'stock_info' => [
                        'available_stock' => 0,
                        'reserved_stock' => 0,
                        'free_stock' => 0,
                        'unit' => $detail->Unit ?? '',
                        'location' => '-',
                        'last_updated' => null
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'details' => $formattedDetails->toArray(),
                'total_items' => $bomDetails->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching BOM details: ' . $e->getMessage()
            ]);
        }
    }

    private function getProcessesFromFormula($formula)
    {
        $processes = [];

        if (strpos($formula, '.PTG.') !== false) $processes[] = 'PTG';
        if (strpos($formula, '.CTK.') !== false) $processes[] = 'CTK';
        if (strpos($formula, '.EPL.') !== false) $processes[] = 'EPL';
        if (strpos($formula, '.EMB.') !== false) $processes[] = 'EMB';
        if (strpos($formula, '.PLG.') !== false) $processes[] = 'PLG';
        if (strpos($formula, '.KPS.') !== false) $processes[] = 'KPS';
        if (strpos($formula, '.STR.') !== false) $processes[] = 'STR';
        if (strpos($formula, '.LEM.') !== false) $processes[] = 'LEM';
        if (strpos($formula, '.TUM.') !== false) $processes[] = 'TUM';
        if (strpos($formula, '.EPL.') !== false) $processes[] = 'EPL';


        return $processes;
    }

    /**
     * Get machine mapping from tb_mapping_items
     */
    public function getMachineMapping(Request $request)
    {
        // dd($request->all());
        try {
            $materialCode = $request->input('materialCode');
            $processType = $request->input('processType');

            Log::info('Getting machine mapping for:', [
                'materialCode' => $materialCode,
                'processType' => $processType
            ]);

            // Cari mapping berdasarkan material code dan process type
            $mapping = null;

            if ($processType === 'CETAK') {
                // Untuk proses CETAK, cari field m_ctk
                $mapping = DB::table('tb_mapping_items')
                    ->where('kode', $materialCode)
                    ->whereNotNull('m_ctk')
                    ->where('m_ctk', '!=', '')
                    ->first();

                // dd($mapping);
                if ($mapping) {
                    return response()->json([
                        'success' => true,
                        'machine' => $mapping->m_ctk,
                        'message' => 'Machine mapping found for CETAK process'
                    ]);
                }
            } elseif ($processType === 'PLONG') {
                // Untuk proses PLONG, cari field m_ptg
                $mapping = DB::table('tb_mapping_items')
                    ->where('kode', $materialCode)
                    ->whereNotNull('m_plg')
                    ->where('m_plg', '!=', '')
                    ->first();

                if ($mapping) {
                    return response()->json([
                        'success' => true,
                        'machine' => $mapping->m_plg,
                        'message' => 'Machine mapping found for PLONG process'
                    ]);
                }
            } elseif ($processType === 'GLUEING') {
                // Untuk proses GLUEING, cari field m_glueing
                $mapping = DB::table('tb_mapping_items')
                    ->where('kode', $materialCode)
                    ->whereNotNull('m_lem')
                    ->where('m_lem', '!=', '')
                    ->first();

                // dd($mapping);
            } else {
                // Untuk proses lainnya, cari field m_hp
                // dd('masuk sini');
            }

            // dd($mapping);
            if ($mapping) {
                return response()->json([
                    'success' => true,
                    'machine' => $mapping->m_lem,
                    'message' => 'Machine mapping found for GLUEING process'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'machine' => 'TIDAK TERSEDIA',
                    'message' => 'Machine mapping found for GLUEING process'
                ]);
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Get machine capacity from mastermachine in mysql3
     */
    public function getMachineCapacity(Request $request)
    {
        try {
            $machineName = $request->input('machine');
            // dd($machineName);

            Log::info('Getting machine capacity for:', [
                'machineName' => $machineName
            ]);

            $machine = Machine::where('Code', $machineName)->first();
            // $machine = DB::connection('mysql3')->table('mastermachine')->where('Code', $machineName)->first();
            // Cari kapasitas mesin dari database mysql3
            // $machine = DB::connection('mysql3')->table('mastermachine')
            //     ->where('Code', $machineName)
            //     ->first();

            // dd($machine);

            if ($machine) {
                // Ambil kapasitas dari field yang sesuai (sesuaikan dengan struktur tabel)
                $capacity = $machine->CapacityPerHour ?? 0;
                // $capacity = $machine->CapacityPerHour ?? 0;

                Log::info('Machine capacity found:', [
                    'machineName' => $machineName,
                    'capacity' => $capacity
                ]);

                return response()->json([
                    'success' => true,
                    'capacity' => $capacity,
                    'unit' => $machine->Unit,
                    'message' => 'Machine capacity found'
                ]);
            }

            // Jika mesin tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Machine not found',
                'capacity' => null,
                'unit' => null
            ]);
        } catch (Exception $e) {
            Log::error('Error getting machine capacity:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error getting machine capacity: ' . $e->getMessage(),
                'capacity' => null,
                'unit' => null
            ]);
        }
    }

    /**
     * Save plan update from timeline table
     */
    public function savePlanUpdate(Request $request)
    {
        // dd($request->all());
        Log::info('Save Plan Update called successfully!');

        try {
            // Validasi input
            $validationRules = [];

            // Validasi untuk updates (wajib memiliki ID)
            if ($request->has('plan_updates') && is_array($request->input('plan_updates')) && count($request->input('plan_updates')) > 0) {
                $validationRules['plan_updates'] = 'required|array';
                $validationRules['plan_updates.*.id'] = 'required|integer';
                $validationRules['plan_updates.*.start_jam'] = 'required|date';
                $validationRules['plan_updates.*.end_jam'] = 'required|date';
            }

            // Validasi untuk creates (tidak perlu ID, tapi harus ada data)
            if ($request->has('plan_creates') && is_array($request->input('plan_creates')) && count($request->input('plan_creates')) > 0) {
                $validationRules['plan_creates'] = 'required|array';
                $validationRules['plan_creates.*.code_item'] = 'required|string';
                $validationRules['plan_creates.*.code_machine'] = 'required|string';
                $validationRules['plan_creates.*.quantity'] = 'required|numeric|min:1';
                $validationRules['plan_creates.*.start_jam'] = 'required|date';
                $validationRules['plan_creates.*.end_jam'] = 'required|date';
            }

            $request->validate($validationRules);

            $planUpdates = $request->input('plan_updates', []);
            $planCreates = $request->input('plan_creates', []);
            $totalUpdates = 0;
            $totalCreates = 0;
            $updatedItems = [];
            $createdItems = [];

            DB::beginTransaction();

            // Handle UPDATES
            foreach ($planUpdates as $update) {
                $planId = $update['id'];

                // Parse dates
                $startJam = date('Y-m-d H:i:s', strtotime($update['start_jam']));
                $endJam = date('Y-m-d H:i:s', strtotime($update['end_jam']));

                // Calculate duration
                $startDate = new \DateTime($startJam);
                $endDate = new \DateTime($endJam);
                $duration = $endDate->diff($startDate);
                $estJam = ($duration->days * 24) + $duration->h + ($duration->i / 60);
                $estDay = $estJam / 24;

                // Prepare update data
                $updateData = [
                    'start_jam' => $startJam,
                    'end_jam' => $endJam,
                    'est_jam' => round($estJam, 2),
                    'est_day' => round($estDay, 2),
                    'flag_status' => $update['flag_status'] ?? 'PLANNED',
                    'changed_by' => auth()->user()->name ?? 'SYSTEM',
                    'updated_at' => now(),
                    'setup' => $update['setup_time'] ?? 0,
                    'istirahat' => $update['istirahat_time'] ?? 0,
                    'catatan_proses' => $update['catatan_proses'] ?? null,
                    'status_item' => $update['status_item'] ?? null,
                    'keterangan_item' => $update['keterangan_item'] ?? null,
                    'job_order' => $update['job_order'] ?? null,
                ];

                // Update code_machine jika ada perubahan mesin
                if (isset($update['code_machine'])) {
                    $updateData['code_machine'] = $update['code_machine'];
                }

                // Update quantity jika ada perubahan
                if (isset($update['quantity'])) {
                    $updateData['quantity'] = $update['quantity'];
                }

                // Update di database
                $updated = DB::table('tb_plan_first_productions')
                    ->where('id', $planId)
                    ->update($updateData);

                if ($updated) {
                    $totalUpdates++;
                    $updatedItems[] = [
                        'id' => $planId,
                        'code_item' => $update['code_item'] ?? null,
                        'code_machine' => $update['code_machine'] ?? null,
                        'quantity' => $update['quantity'] ?? null,
                        'start_jam' => $startJam,
                        'end_jam' => $endJam
                    ];
                }
            }

            // Handle CREATES (new items)
            foreach ($planCreates as $create) {
                // Parse dates
                $startJam = date('Y-m-d H:i:s', strtotime($create['start_jam']));
                $endJam = date('Y-m-d H:i:s', strtotime($create['end_jam']));

                // Calculate duration
                $startDate = new \DateTime($startJam);
                $endDate = new \DateTime($endJam);
                $duration = $endDate->diff($startDate);
                $estJam = ($duration->days * 24) + $duration->h + ($duration->i / 60);
                $estDay = $estJam / 24;

                // dd($create);

                // Get machine capacity for up_cetak
                $machine = DB::table('tb_machines')->where('Code', $create['code_machine'])->first();
                // dd($machine);
                $capacity = $machine ? ($machine->capacityPerHour ?? 10000) : 10000;

                // Insert new plan
                $newPlanId = DB::table('tb_plan_first_productions')->insertGetId([
                    'code_plan' => 'MANUAL-' . date('YmdHis') . '-' . rand(1000, 9999),
                    'code_item' => $create['code_item'],
                    'material_name' => $create['material_name'] ?? null,
                    'code_machine' => $create['code_machine'],
                    'quantity' => $create['quantity'],
                    'up_cetak' => $create['up_cetak'] ?? 1,
                    'capacity' => $capacity,
                    'est_jam' => round($estJam, 2),
                    'est_day' => round($estDay, 2),
                    'start_jam' => $startJam,
                    'end_jam' => $endJam,
                    'delivery_date' => $create['delivery_date'] ?? null,
                    'wo_docno' => $create['wo_docno'] ?? null,
                    'so_docno' => $create['so_docno'] ?? null,
                    'process' => $create['process'] ?? 'CTK',
                    'flag_status' => $create['flag_status'] ?? 'PLANNED',
                    'catatan_proses' => $create['catatan_proses'] ?? null,
                    'status_item' => $create['status_item'] ?? null,
                    'keterangan_item' => $create['keterangan_item'] ?? null,
                    'job_order' => $create['job_order'] ?? null,
                    'created_by' => auth()->user()->name ?? 'SYSTEM',
                    'changed_by' => auth()->user()->name ?? 'SYSTEM',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                if ($newPlanId) {
                    $totalCreates++;
                    $createdItems[] = [
                        'id' => $newPlanId,
                        'code_item' => $create['code_item'],
                        'code_machine' => $create['code_machine'],
                        'quantity' => $create['quantity'],
                        'start_jam' => $startJam,
                        'end_jam' => $endJam
                    ];

                    Log::info('Plan created successfully:', [
                        'id' => $newPlanId,
                        'code_item' => $create['code_item'],
                        'code_machine' => $create['code_machine'],
                        'quantity' => $create['quantity'],
                        'start_jam' => $startJam,
                        'end_jam' => $endJam
                    ]);
                }
            }

            // Save machine shift configurations (within the same transaction)
            if ($request->has('machine_shift_configs') && is_array($request->input('machine_shift_configs'))) {
                $shiftConfigs = $request->input('machine_shift_configs');
                foreach ($shiftConfigs as $machineCode => $numShifts) {
                    \App\Models\MachineShift::setShift($machineCode, (int)$numShifts);
                }
                Log::info('Machine shift configurations saved:', $shiftConfigs);
            }

            DB::commit();

            Log::info('Plan updates/creates completed:', [
                'total_updates' => $totalUpdates,
                'total_creates' => $totalCreates,
                'requested_updates' => count($planUpdates),
                'requested_creates' => count($planCreates)
            ]);

            $message = [];
            if ($totalUpdates > 0) {
                $message[] = "{$totalUpdates} plan diupdate";
            }
            if ($totalCreates > 0) {
                $message[] = "{$totalCreates} plan baru dibuat";
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menyimpan: " . implode(', ', $message),
                'total_updated' => $totalUpdates,
                'total_created' => $totalCreates,
                'updated_items' => $updatedItems,
                'created_items' => $createdItems,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (ValidationException $e) {
            DB::rollback();
            Log::error('Validation error in savePlanUpdate:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error saving plan update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan perubahan plan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveFinishJob(Request $request)
    {
        Log::info('Save Finish Job called successfully!');

        $data = $request->all();

        // Validasi input
        $request->validate([
            'item_id' => 'required|integer',
            'code_item' => 'required|string',
            'material_name' => 'nullable|string',
            'wo_docno' => 'nullable|string',
            'so_docno' => 'nullable|string',
            'target_quantity' => 'required|numeric',
            'production_result' => 'required|numeric',
            'quality_status' => 'required|in:OK,NG,PARTIAL',
            'defect_quantity' => 'nullable|numeric',
            'defect_reason' => 'nullable|string',
            'machine_code' => 'required|string',
            'process' => 'nullable|string',
            'department' => 'nullable|string',
            'finish_date' => 'required|date',
            'remaining_action' => 'nullable|in:reschedule,keep,cancel',
            'reschedule_date' => 'nullable|date',
            'reschedule_machine' => 'nullable|string',
            'keep_date' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan finish job ke tb_plan_continued_productions
            $continuedProductionId = DB::table('tb_plan_continued_productions')->insertGetId([
                'id_first_plan' => (string) $data['item_id'],
                'code_plan' => 'FINISH_' . $data['item_id'] . '_' . now()->format('YmdHis'),
                'code_item' => $data['code_item'],
                'code_machine' => $data['machine_code'],
                'quantity' => (string) $data['production_result'], // Quantity yang berhasil diproduksi
                'target_quantity' => (string) $data['target_quantity'],
                'production_result' => (string) $data['production_result'],
                'quality_status' => $data['quality_status'],
                'defect_quantity' => (string) ($data['defect_quantity'] ?? 0),
                'defect_reason' => $data['defect_reason'] ?? null,
                'material_name' => $data['material_name'] ?? null,
                'wo_docno' => $data['wo_docno'] ?? null,
                'so_docno' => $data['so_docno'] ?? null,
                'process' => $data['process'] ?? null,
                'department' => $data['department'] ?? null,
                'finish_time' => $data['finish_date'],
                'remaining_action' => $data['remaining_action'] ?? null,
                'reschedule_date' => $data['reschedule_date'] ?? null,
                'reschedule_machine' => $data['reschedule_machine'] ?? null,
                'keep_date' => $data['keep_date'] ?? null,
                'flag_status' => 'FINISHED',
                'status_proses' => 'FINISHED',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // 2. Update status di tb_plan_first_productions
            $updated = DB::table('tb_plan_first_productions')
                ->where('id', $data['item_id'])
                ->update([
                    'flag_status' => 'FINISH',
                    'updated_at' => now()
                ]);

            // 3. Handle sisa quantity - buat item baru jika ada sisa
            $remainingQty = $data['target_quantity'] - $data['production_result'];
            $newItemId = null;

            Log::info('Finish Job - Remaining quantity check:', [
                'target_quantity' => $data['target_quantity'],
                'production_result' => $data['production_result'],
                'remaining_qty' => $remainingQty,
                'remaining_action' => $data['remaining_action']
            ]);

            if ($remainingQty > 0 && $data['remaining_action'] === 'reschedule') {
                // Ambil data item asli untuk membuat item baru
                $originalItem = DB::table('tb_plan_first_productions')
                    ->where('id', $data['item_id'])
                    ->first();

                if ($originalItem) {
                    // Buat item baru untuk sisa quantity
                    $newItemId = DB::table('tb_plan_first_productions')->insertGetId([
                        'code_plan' => $originalItem->code_plan,
                        'code_item' => $originalItem->code_item,
                        'code_machine' => $data['reschedule_machine'] ?? $data['machine_code'], // Gunakan mesin yang sama atau mesin baru
                        'quantity' => $remainingQty,
                        'up_cetak' => $originalItem->up_cetak,
                        'capacity' => $originalItem->capacity,
                        'est_jam' => $originalItem->est_jam,
                        'est_day' => $originalItem->est_day,
                        'start_jam' => $data['reschedule_date'] ? $data['reschedule_date'] . ' 08:00:00' : now()->addDay()->format('Y-m-d') . ' 08:00:00',
                        'end_jam' => null, // Akan dihitung otomatis
                        'flag_status' => 'RESCHEDULED',
                        'wo_docno' => $originalItem->wo_docno,
                        'so_docno' => $originalItem->so_docno,
                        'delivery_date' => $originalItem->delivery_date,
                        'process' => $originalItem->process,
                        'material_name' => $originalItem->material_name,
                        'created_by' => auth()->user()->name ?? 'System',
                        'changed_by' => auth()->user()->name ?? 'System',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Hitung end_jam untuk item baru
                    if ($newItemId) {
                        $capacity = floatval($originalItem->capacity ?? 10000);
                        $durationHours = $remainingQty / $capacity;
                        $startTime = new \DateTime($data['reschedule_date'] ? $data['reschedule_date'] . ' 08:00:00' : now()->addDay()->format('Y-m-d') . ' 08:00:00');
                        $endTime = clone $startTime;
                        $endTime->add(new \DateInterval('PT' . round($durationHours * 3600) . 'S'));

                        DB::table('tb_plan_first_productions')
                            ->where('id', $newItemId)
                            ->update([
                                'end_jam' => $endTime->format('Y-m-d H:i:s'),
                                'est_jam' => $durationHours, // Pastikan est_jam juga diupdate
                                'updated_at' => now()
                            ]);

                        Log::info('Finish Job - New rescheduled item created:', [
                            'new_item_id' => $newItemId,
                            'remaining_qty' => $remainingQty,
                            'reschedule_machine' => $data['reschedule_machine'] ?? $data['machine_code'],
                            'reschedule_date' => $data['reschedule_date'],
                            'start_jam' => $data['reschedule_date'] ? $data['reschedule_date'] . ' 08:00:00' : now()->addDay()->format('Y-m-d') . ' 08:00:00',
                            'end_jam' => $endTime->format('Y-m-d H:i:s'),
                            'flag_status' => 'RESCHEDULED'
                        ]);
                    }
                }
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Finish Job berhasil disimpan!',
                'data' => [
                    'original_item_id' => $data['item_id'],
                    'continued_production_id' => $continuedProductionId,
                    'new_item_id' => $newItemId,
                    'remaining_quantity' => $remainingQty,
                    'production_result' => $data['production_result'],
                    'quality_status' => $data['quality_status'],
                    'machine_code' => $data['machine_code']
                ]
            ];

            Log::info('Finish Job saved successfully', $response);
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving finish job: ' . $e->getMessage());

            $errorResponse = [
                'success' => false,
                'error' => 'Gagal menyimpan finish job',
                'message' => $e->getMessage(),
                'data_received' => $data,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ];

            return response()->json($errorResponse, 500);
        }
    }

    public function getHolidays()
    {
        try {
            $holidays = DB::table('tb_holiday_days')
                ->where('is_active', true)
                ->select('date', 'override_type', 'working_hours', 'description')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $holidays
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching holidays: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data holiday: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get holiday data for timeline background
     */
    public function getHolidayData()
    {
        try {
            $currentYear = date('Y');
            $currentMonth = date('m');

            // Ambil data holiday dari database
            $holidays = DB::table('tb_holiday_days')
                ->where('is_active', true)
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $currentMonth)
                ->get()
                ->map(function ($holiday) {
                    // Validate date format
                    try {
                        $date = \Carbon\Carbon::parse($holiday->date);
                        return [
                            'date' => $date->format('Y-m-d'),
                            'type' => $holiday->override_type ?? 'national_holiday',
                            'name' => $holiday->description ?? 'Hari Libur'
                        ];
                    } catch (\Exception $dateError) {
                        Log::warning('Invalid holiday date format: ' . $holiday->date);
                        return null;
                    }
                })
                ->filter() // Remove null values
                ->values();

            // Generate hari Minggu untuk bulan ini
            $sundays = $this->generateSundaysForMonth($currentYear, $currentMonth);

            // Gabungkan semua holiday
            $allHolidays = $holidays->merge($sundays);

            Log::info('Holiday data retrieved successfully', [
                'total_holidays' => $allHolidays->count(),
                'database_holidays' => $holidays->count(),
                'generated_sundays' => $sundays->count(),
                'month' => $currentMonth,
                'year' => $currentYear
            ]);

            return response()->json([
                'success' => true,
                'holidays' => $allHolidays->values(),
                'message' => 'Holiday data retrieved successfully',
                'month' => $currentMonth,
                'year' => $currentYear
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving holiday data: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving holiday data: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }

    /**
     * Generate Sundays for specific month
     */
    private function generateSundaysForMonth($year, $month)
    {
        $sundays = collect();
        $date = new \DateTime("$year-$month-01");

        // Cari Minggu pertama di bulan ini
        while ($date->format('N') != 7) {
            $date->add(new \DateInterval('P1D'));
        }

        // Generate semua Minggu di bulan ini
        while ($date->format('m') == $month) {
            $sundays->push([
                'date' => $date->format('Y-m-d'),
                'type' => 'sunday',
                'name' => 'Minggu'
            ]);
            $date->add(new \DateInterval('P7D'));
        }

        return $sundays;
    }

    public function getPlanPreview(Request $request)
    {
        try {
            Log::info('=== GET PLAN PREVIEW ===');
            Log::info('Request data:', $request->all());

            $request->validate([
                'date' => 'required|date',
                'machine_code' => 'required|string'
            ]);

            $date = $request->input('date');
            $machineCode = $request->input('machine_code');

            Log::info('Searching for plans:', [
                'date' => $date,
                'machine_code' => $machineCode
            ]);

            // Get plans for the specified date and machine
            $query = DB::table('tb_plan_first_productions')
                ->where('code_machine', $machineCode)
                ->whereDate('start_jam', $date);

            // Filter berdasarkan process jika ada (untuk GLUEING/LEM)
            $process = $request->input('process');
            if ($process && in_array(strtoupper($process), ['GLUEING', 'GLU', 'LEM'])) {
                $query->where(function ($q) {
                    $q->where('process', 'LEM')
                        ->orWhere('process', 'GLUEING')
                        ->orWhere('process', 'GLU')
                        ->orWhere('process', 'LIKE', '%LEM%')
                        ->orWhere('process', 'LIKE', '%GLUEING%')
                        ->orWhere('process', 'LIKE', '%GLU%');
                });
            }

            $plans = $query->orderBy('start_jam', 'asc')
                ->select([
                    'id',
                    'code_item',
                    'material_name',
                    'quantity',
                    'start_jam',
                    'end_jam',
                    'wo_docno',
                    'so_docno',
                    'flag_status',
                    'catatan_proses',
                    'process'
                ])
                ->get();

            Log::info('Found plans:', [
                'count' => $plans->count(),
                'plans' => $plans->toArray()
            ]);

            return response()->json([
                'success' => true,
                'plans' => $plans,
                'date' => $date,
                'machine_code' => $machineCode,
                'total_count' => $plans->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting plan preview:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data plan preview: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLubricationMaintenance(Request $request)
    {
        // dd($request->all());
        try {
            // Get machine parameter from request
            $machineName = $request->input('machine');

            $id_mesin = DB::connection('mysql2')
                ->table('tmesin')
                ->where('namaMesinSim', $machineName)
                ->select('namaMesin')
                ->first();

            // dd($id_mesin);
            // Build query
            $query = DB::connection('mysql2')
                ->table('tpenjadwalan')
                ->join('tmesin', 'tmesin.namaMesin', '=', 'tpenjadwalan.mesin')
                ->select([
                    'id',
                    'title',
                    'datetgs',
                    'isi_tugas',
                    'idUnit',
                    'title_only',
                    'status_tugas',
                    'isi_tugas',
                    'mesin',
                    'pelumasan_shift',
                    'tmesin.namaMesin as namaMesin',
                    'tmesin.codeMesinSim as namaMesinSim'
                ])
                ->where('title_only', 'Pelumasan')
                ->where('status_tugas', 'PLAN')
                ->whereDate('datetgs', '>=', now()->subDays(30));

            // Filter by machine if specified
            if ($machineName && $machineName !== '') {
                $query->where('mesin', $id_mesin->namaMesin);
            }

            $lubricationData = $query->orderBy('datetgs', 'asc')->get();

            Log::info('Found lubrication data:', [
                'count' => $lubricationData->count(),
                'machine_filter' => $machineName,
                'data' => $lubricationData->toArray()
            ]);

            return response()->json([
                'success' => true,
                'lubrication_data' => $lubricationData,
                'machine_name' => $machineName,
                'total_count' => $lubricationData->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting lubrication maintenance data:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pelumasan maintenance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLubricationMachines()
    {
        try {
            $machines = DB::connection('mysql2')
                ->table('tmesin')
                ->orderBy('namaMesin')
                ->get();

            return response()->json([
                'success' => true,
                'machines' => $machines
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get plan continued production data for PLONG processes
     */
    public function getPlanContinuedData(Request $request)
    {
        try {
            $data = DB::table('tb_plan_continued_productions')
                ->whereNotNull('start_jam')
                ->whereNotNull('end_jam')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'code_plan' => $plan->code_plan,
                        'code_machine' => $plan->code_machine,
                        'code_item' => $plan->code_item,
                        'name_item' => DB::connection('mysql3')
                            ->table('mastermaterial')
                            ->where('Code', $plan->code_item)
                            ->select('Name')
                            ->first()
                            ->Name ?? '',
                        'wo_docno' => $plan->wo_docno,
                        'so_docno' => $plan->so_docno,
                        'quantity' => $plan->quantity,
                        'start_jam' => $plan->start_jam ? date('Y-m-d\TH:i:s', strtotime($plan->start_jam)) : null,
                        'end_jam' => $plan->end_jam ? date('Y-m-d\TH:i:s', strtotime($plan->end_jam)) : null,
                        'est_jam' => $plan->est_jam,
                        'flag_status' => $plan->flag_status,
                        'process' => $plan->process,
                        'delivery_date' => $plan->delivery_date,
                        'material_name' => $plan->material_name,
                        'capacity' => $plan->capacity,
                    ];
                });

            Log::info('Plan Continued Data for PLONG:', ['data' => $data->toArray()]);

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting plan continued data:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error getting plan continued data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function savePriorityChanges(Request $request)
    {
        dd($request->all());
    }

    /**
     * Search materials from sim_krisanthium.mastermaterial
     * For use with Select2 autocomplete
     */
    public function searchMaterials(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $page = $request->get('page', 1);
            $perPage = 20;
            $exact = $request->get('exact', false);

            $query = DB::connection('mysql3')
                ->table('mastermaterial')
                ->select('Code', 'Name');

            if ($exact) {
                // For exact match (when auto-filling from code item)
                $query->where('Code', $search);
            } else {
                // For search/autocomplete
                $query->where(function ($q) use ($search) {
                    $q->where('Code', 'like', '%' . $search . '%')
                        ->orWhere('Name', 'like', '%' . $search . '%');
                });
            }

            $query->orderBy('Name', 'asc');

            $total = $query->count();
            $materials = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            $results = $materials->map(function ($material) {
                return [
                    'id' => $material->Code,
                    'text' => $material->Code . ' - ' . $material->Name,
                    'Code' => $material->Code,
                    'Name' => $material->Name
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching materials: ' . $e->getMessage());
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false]
            ], 500);
        }
    }

    /**
     * Send Job Order to Machine Database
     */
    public function sendJobOrderToMachine(Request $request)
    {
        // dd($request->all());
        try {
            $machineCode = $request->input('machine_code');
            $jobOrders = $request->input('job_orders', []);

            if (!$machineCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Machine code is required'
                ], 400);
            }

            if (empty($jobOrders)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No job orders to send'
                ], 400);
            }

            // Tentukan koneksi database berdasarkan machine code
            // Untuk mesin CD6, gunakan mysql9, selain itu gunakan mysql8
            $dbConnection = ($machineCode === 'CD6') ? 'mysql9' : 'mysql8';

            Log::info("Using database connection for machine", [
                'machine_code' => $machineCode,
                'connection' => $dbConnection
            ]);

            // Gunakan koneksi yang sudah ditentukan
            $savedCount = 0;
            $errors = [];
            $validJobOrders = [];

            // Step 1: Validasi dan kumpulkan semua JO yang valid
            foreach ($jobOrders as $jo) {
                $jobOrderNo = $jo['job_order'] ?? $jo['job_order_no'] ?? null;
                $quantity = $jo['quantity'] ?? 0;
                $woDocno = $jo['wo_docno'] ?? null;

                if (!$jobOrderNo) {
                    $errors[] = "Job Order number is missing for item ID: " . ($jo['id'] ?? 'unknown');
                    continue;
                }

                $validJobOrders[] = [
                    'job_order_no' => $jobOrderNo,
                    'quantity' => (int)$quantity,
                    'wo_docno' => $woDocno,
                    'original_data' => $jo
                ];
            }

            if (empty($validJobOrders)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada Job Order yang valid untuk dikirim',
                    'errors' => $errors
                ], 400);
            }

            try {

                if ($machineCode == 'CD6') {
                    // Step 2: Foreach delete dulu - hapus semua JO yang sudah ada dengan status ORDER
                    $deletedCount = 0;
                    foreach ($validJobOrders as $jo) {
                        $jobOrderNo = $jo['job_order_no'];

                        $deleted = DB::connection('mysql9')
                            ->table('order_tb')
                            ->where('jo', $jobOrderNo)
                            ->where('status', 'ORDER')
                            ->delete();

                        if ($deleted > 0) {
                            $deletedCount += $deleted;
                            Log::info("Deleted existing Job Order", [
                                'job_order' => $jobOrderNo,
                                'machine_code' => $machineCode
                            ]);
                        }
                    }

                    if ($deletedCount > 0) {
                        Log::info("Total deleted Job Orders before insert", [
                            'deleted_count' => $deletedCount,
                            'machine_code' => $machineCode
                        ]);
                    }

                    // dd($validJobOrders);

                    // Step 3: Foreach insert - insert semua JO yang dipilih
                    foreach ($validJobOrders as $jo) {
                        DB::connection($dbConnection)
                            ->table('order_tb')
                            ->insert([
                                'jo' => $jo['job_order_no'],
                                'wo' => $jo['wo_docno'],
                                'totprod' => $jo['quantity'],
                                'status' => 'ORDER',
                                // 'username' => 'miarti',
                                'lineprod' => 'CD61',
                                // 'datetimes' => Carbon::now()->format('Y/m/d H:i:s'),
                                // 'datetime' => (string) Carbon::now()->timestamp
                            ]);

                        $savedCount++;

                        Log::info("Job Order sent to remote database", [
                            'job_order' => $jo['job_order_no'],
                            'quantity' => $jo['quantity'],
                            'machine_code' => $machineCode
                        ]);
                    }
                } else {


                    // Step 2: Foreach delete dulu - hapus semua JO yang sudah ada dengan status ORDER
                    $deletedCount = 0;
                    foreach ($validJobOrders as $jo) {
                        $jobOrderNo = $jo['job_order_no'];

                        $deleted = DB::connection($dbConnection)
                            ->table('order_tb')
                            ->where('jo', $jobOrderNo)
                            ->where('status', 'ORDER')
                            ->delete();

                        if ($deleted > 0) {
                            $deletedCount += $deleted;
                            Log::info("Deleted existing Job Order", [
                                'job_order' => $jobOrderNo,
                                'machine_code' => $machineCode
                            ]);
                        }
                    }

                    if ($deletedCount > 0) {
                        Log::info("Total deleted Job Orders before insert", [
                            'deleted_count' => $deletedCount,
                            'machine_code' => $machineCode
                        ]);
                    }
                }
                // dd($validJobOrders);

                // Step 3: Foreach insert - insert semua JO yang dipilih
                foreach ($validJobOrders as $jo) {
                    DB::connection($dbConnection)
                        ->table('order_tb')
                        ->insert([
                            'jo' => $jo['job_order_no'],
                            'wo' => $jo['wo_docno'],
                            'totprod' => $jo['quantity'],
                            'status' => 'ORDER',
                            // 'username' => 'miarti',
                            'lineprod' => 'CD63',
                            // 'datetimes' => Carbon::now()->format('Y/m/d H:i:s'),
                            // 'datetime' => (string) Carbon::now()->timestamp
                        ]);

                    $savedCount++;

                    Log::info("Job Order sent to remote database", [
                        'job_order' => $jo['job_order_no'],
                        'quantity' => $jo['quantity'],
                        'machine_code' => $machineCode
                    ]);
                }

                Log::info("All Job Orders sent to remote database", [
                    'total_sent' => $savedCount,
                    'machine_code' => $machineCode,
                    'deleted_before' => $deletedCount
                ]);
            } catch (\Exception $e) {
                $errors[] = "Error processing Job Orders: " . $e->getMessage();
                Log::error("Error sending Job Orders to machine", [
                    'machine_code' => $machineCode,
                    'job_orders' => $validJobOrders,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            if ($savedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengirim {$savedCount} Job Order ke database mesin {$machineCode}",
                    'saved_count' => $savedCount,
                    'total' => count($jobOrders),
                    'errors' => $errors
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim Job Order. Tidak ada data yang berhasil disimpan.',
                    'errors' => $errors
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error sending Job Order to machine", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Job Orders with status OPEN from remote database for monitoring
     */
    public function getOpenJobOrdersFromRemote(Request $request)
    {
        // dd($request->all());
        try {
            // Ambil machine_code dari request (opsional, jika tidak ada gunakan default)
            $machineCode = $request->input('machine_code');

            // Tentukan koneksi database berdasarkan machine code
            // Untuk mesin CD6, gunakan mysql9, selain itu gunakan mysql8
            $dbConnection = ($machineCode === 'CD6') ? 'mysql9' : 'mysql8';

            Log::info("Getting OPEN Job Orders from remote database", [
                'machine_code' => $machineCode,
                'connection' => $dbConnection
            ]);

            if ($machineCode == 'CD6') {
                $openJobOrders = DB::connection('mysql9')
                    ->table('order_tb')
                    ->where('status', 'ORDER')
                    // ->whereDate('datetimes', Carbon::today())
                    ->orderBy('id', 'desc')
                    ->limit(100) // Limit untuk performa
                    ->get([
                        'id',
                        'jo',
                        'wo',
                        'totprod',
                        'status',
                    ]);
            } else {
                // Get Job Orders with status ORDER
                $openJobOrders = DB::connection('mysql8')
                    ->table('order_tb')
                    ->where('status', 'ORDER')
                    // ->whereDate('datetimes', Carbon::today())
                    ->orderBy('id', 'desc')
                    ->limit(100) // Limit untuk performa
                    ->get([
                        'id',
                        'jo',
                        'wo',
                        'totprod',
                        'status',
                    ]);
            }

            // dd($openJobOrders);

            return response()->json([
                'success' => true,
                'data' => $openJobOrders,
                'count' => count($openJobOrders)
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting OPEN Job Orders from remote database", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
