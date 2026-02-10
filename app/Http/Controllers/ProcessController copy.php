<?php

namespace App\Http\Controllers;

use App\Models\PelumasanMaintenance;
use App\Models\PlanContinuedProduction;
use App\Models\PlanFirstProduction;
use App\Models\RencanaPlan;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Machine;
use App\Models\MappingItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProcessController extends Controller
{
    public function pilihRencanaPlan()
    {
        return view('main.process.pilih-rencana-plan');
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

            $cacheKey = "salesOrders_{$fromdate}_{$todate}_{$smSelected}";
            $salesOrder = Cache::remember($cacheKey, 60, function () use ($fromdate, $todate) {
                if ($fromdate && $todate) {
                    return DB::connection('mysql3')
                        ->table('salesorderh')
                        ->whereIn('series', ['SOD', 'SOP'])
                        ->whereIn('status', ['approved'])
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

            $SOWODocNo = [];

            // Ambil daftar WO, SO, kode item yang sudah pernah di-plan
            $planned = PlanFirstProduction::select('wo_docno', 'so_docno', 'code_item')->get();
            $plannedSet = $planned->map(function ($row) {
                return $row->wo_docno . '|' . $row->so_docno . '|' . $row->code_item;
            })->toArray();

            if ($salesOrder->isNotEmpty()) {
                $salesOrderDocNos = $salesOrder->pluck('DocNo')->toArray();

                $workOrders = DB::connection('mysql3')
                    ->table('workorderh')
                    ->leftJoin('workorderd', 'workorderh.DocNo', 'workorderd.DocNo')
                    ->whereIn('SODocNo', $salesOrderDocNos)
                    ->select('workorderh.DocNo', 'workorderh.MaterialCode', 'workorderh.Zupcetak', 'workorderh.Unit', 'workorderd.Qty', 'workorderh.SODocNo')
                    ->get();

                if ($workOrders->isNotEmpty()) {
                    $workOrderDocNos = $workOrders->pluck('DocNo')->toArray();

                    $unfinishedJobOrders = DB::connection('mysql3')
                        ->table('joborder')
                        ->whereIn('WODocNo', $workOrderDocNos)
                        ->whereNotIn('status', ['FINISH', 'DELETED', 'IN PROGRESS'])
                        ->pluck('WODocNo')
                        ->toArray();

                    $additionalJobOrders = DB::connection('mysql3')
                        ->table('joborder')
                        ->whereIn('IODocNo', $workOrderDocNos)
                        ->whereNotIn('status', ['FINISH', 'DELETED', 'IN PROGRESS'])
                        ->pluck('IODocNo')
                        ->toArray();

                    $unfinishedJobOrders = array_merge($unfinishedJobOrders, $additionalJobOrders);

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

            return DataTables::of($SOWODocNo)
                ->addIndexColumn()
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
                ->rawColumns(['Detail'])
                ->make(true);
        }
    }

    public function submitPlanFirst(Request $request)
    {

        $data = $request->data;
        $startDate = $request->start_date;
        $userChoices = $request->user_choices ?? [];

        Log::info('User Choices:', $userChoices);
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

        $isFinalData = true;
        foreach ($data as $item) {
            if (!isset($item['processes'])) {
                $isFinalData = false;
                break;
            }
        }

        $allConflictsResolved = false;
        if (!empty($userChoices)) {
            $itemsWithConflicts = [];
            foreach ($data as $item) {
                $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                    ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                    ->get();
                $hasPTG = false;
                $hasTUM = false;
                foreach ($masterBomHeader as $bom) {
                    $formula = $bom->Formula;
                    if (strpos($formula, '.PTG.') !== false) {
                        $hasPTG = true;
                    }
                    if (strpos($formula, '.TUM.') !== false) {
                        $hasTUM = true;
                    }
                }
                if ($hasPTG && $hasTUM) {
                    $itemsWithConflicts[] = $item['MaterialCode'];
                }
            }
            $allResolved = true;
            foreach ($itemsWithConflicts as $conflictItem) {
                $hasChoice = false;
                foreach ($userChoices as $choice) {
                    if ($choice['material_code'] === $conflictItem) {
                        $hasChoice = true;
                        break;
                    }
                }
                if (!$hasChoice) {
                    $allResolved = false;
                    break;
                }
            }
            if ($allResolved) {
                $allConflictsResolved = true;
                Log::info('All conflicts resolved - all items with conflicts have been chosen');
                Log::info('Items with conflicts:', $itemsWithConflicts);
                Log::info('User choices:', $userChoices);
            } else {
                Log::info('Not all conflicts resolved yet');
                Log::info('Items with conflicts:', $itemsWithConflicts);
                Log::info('User choices:', $userChoices);
            }
        }

        if ($isFinalData || $allConflictsResolved) {
            // Data sudah hasil pilihan user, langsung proses simpan plan
            $detailProsesItem = [];
            foreach ($data as $item) {
                // Cek apakah ada pilihan user untuk item ini
                $userChoice = null;
                foreach ($userChoices as $choice) {
                    if ($choice['material_code'] === $item['MaterialCode']) {
                        $userChoice = $choice['choice'];
                        break;
                    }
                }
                // Gunakan helper untuk generate processes
                $item['processes'] = $this->generateProcesses($item, $userChoice);
                // FIX: Gunakan kombinasi MaterialCode_WODocNo sebagai key agar tidak saling menimpa
                $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'];
                Log::info('BACKEND FIX: Using unique key for item', [
                    'MaterialCode' => $item['MaterialCode'],
                    'WODocNo' => $item['WODocNo'],
                    'uniqueKey' => $uniqueKey
                ]);
                $detailProsesItem[$uniqueKey] = $item;
            }
            Log::info('All conflicts resolved, calling continuePlanProcessing');
            return $this->continuePlanProcessing($detailProsesItem, $startDate, $code_plan);
        }

        $detailProsesItem = [];
        $pendingItems = [];
        $conflictItems = [];
        $processedItems = [];
        $itemsWithConflict = [];

        // Kumpulkan semua item yang butuh opsi PTG/TUM di awal
        foreach ($data as $item) {
            $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                ->get();
            $hasPTG = false;
            $hasTUM = false;
            foreach ($masterBomHeader as $bom) {
                $formula = $bom->Formula;
                if (strpos($formula, '.PTG.') !== false) $hasPTG = true;
                if (strpos($formula, '.TUM.') !== false) $hasTUM = true;
            }
            if ($hasPTG && $hasTUM) $itemsWithConflict[] = $item['MaterialCode'];
        }

        // Hitung user_choices yang memang untuk item konflik (pakai trim+strtoupper)
        $confirmed = 0;
        foreach ($itemsWithConflict as $conflictCode) {
            foreach ($userChoices as $choice) {
                if (trim(strtoupper($choice['material_code'])) === trim(strtoupper($conflictCode))) {
                    $confirmed++;
                    break;
                }
            }
        }

        // Debug log
        Log::info('DEBUG PTG/TUM: itemsWithConflict', $itemsWithConflict);
        Log::info('DEBUG PTG/TUM: userChoices', $userChoices);
        Log::info('DEBUG PTG/TUM: confirmed', [$confirmed]);
        Log::info('DEBUG PTG/TUM: total_conflict', [count($itemsWithConflict)]);
        Log::info('DEBUG PTG/TUM: confirmed vs total', [$confirmed, count($itemsWithConflict)]);

        // Jika jumlah konfirmasi belum sama dengan jumlah item konflik, tampilkan modal untuk item berikutnya
        if ($confirmed < count($itemsWithConflict)) {
            foreach ($itemsWithConflict as $conflictCode) {
                $found = false;
                foreach ($userChoices as $choice) {
                    if (trim(strtoupper($choice['material_code'])) === trim(strtoupper($conflictCode))) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    Log::info('DEBUG PTG/TUM: Akan tampilkan modal untuk', ['material_code' => $conflictCode]);
                    foreach ($data as $item) {
                        if (trim(strtoupper($item['MaterialCode'])) === trim(strtoupper($conflictCode))) {
                            $prosesData = $this->generateProcesses($item, null, true); // true = hanya PTG/TUM
                            return response()->json([
                                'success' => false,
                                'message' => 'Terdapat proses PTG dan TUM pada item ' . $item['MaterialCode'] . ', silakan pilih opsi:',
                                'options' => [
                                    'ptg' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'PTG')),
                                    'tum' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'TUM')),
                                    'choices' => ['PTG saja', 'TUM saja', 'PTG dan TUM']
                                ],
                                'item' => $item,
                                'choice_values' => ['ptg_only', 'tum_only', 'both'],
                                'all_conflicts' => $itemsWithConflict
                            ]);
                        }
                    }
                }
            }
        }
        // Jika sudah semua, lanjut proses normal

        $unresolvedItems = [];
        foreach ($itemsWithConflict as $conflictCode) {
            $found = false;
            foreach ($userChoices as $choice) {
                if ($choice['material_code'] === $conflictCode) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $unresolvedItems[] = $conflictCode;
            }
        }

        if (!empty($unresolvedItems)) {
            $firstUnresolved = $unresolvedItems[0];
            foreach ($data as $item) {
                if ($item['MaterialCode'] === $firstUnresolved) {
                    $prosesData = $this->generateProcesses($item, null, true); // true = hanya PTG/TUM
                    return response()->json([
                        'success' => false,
                        'message' => 'Terdapat proses PTG dan TUM pada item ' . $item['MaterialCode'] . ', silakan pilih opsi:',
                        'options' => [
                            'ptg' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'PTG')),
                            'tum' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'TUM')),
                            'choices' => ['PTG saja', 'TUM saja', 'PTG dan TUM']
                        ],
                        'item' => $item,
                        'choice_values' => ['ptg_only', 'tum_only', 'both'],
                        'all_conflicts' => $itemsWithConflict
                    ]);
                }
            }
        }
        // Jika sudah semua, lanjut proses normal

        foreach ($data as $item) {
            // dd($item);
            // Cek apakah ada pilihan user untuk item ini
            $userChoice = null;
            foreach ($userChoices as $choice) {
                if ($choice['material_code'] === $item['MaterialCode']) {
                    $userChoice = $choice['choice'];
                    break;
                }
            }
            $item['processes'] = $this->generateProcesses($item, $userChoice);
            // FIX: Gunakan kombinasi MaterialCode_WODocNo sebagai key agar tidak saling menimpa
            $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'];
            Log::info('BACKEND FIX: Using unique key for item (second path)', [
                'MaterialCode' => $item['MaterialCode'],
                'WODocNo' => $item['WODocNo'],
                'uniqueKey' => $uniqueKey
            ]);
            $detailProsesItem[$uniqueKey] = [
                'SODocNo' => $item['SODocNo'],
                'WODocNo' => $item['WODocNo'],
                'MaterialCode' => $item['MaterialCode'],
                'MaterialName' => $item['Name'],
                'Quantity' => $item['Quantity'],
                'Up' => $item['Up'],
                'Unit' => $item['Unit'],
                'DeliveryDate' => $item['DeliveryDate'],
                'Status' => $item['Status'],
                'processes' => $item['processes']
            ];
        }


        // dd('ini detailProsesItem', $detailProsesItem);
        // ... existing code ...

        $detailProsesItem = [];
        $pendingItems = [];

        // Pertama, cek semua item untuk konflik PTG/TUM
        $conflictItems = [];
        $processedItems = [];
        $itemsWithConflict = [];

        // Kumpulkan semua item yang butuh opsi PTG/TUM di awal
        foreach ($data as $item) {
            $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                ->get();
            $hasPTG = false;
            $hasTUM = false;
            foreach ($masterBomHeader as $bom) {
                $formula = $bom->Formula;
                if (strpos($formula, '.PTG.') !== false) $hasPTG = true;
                if (strpos($formula, '.TUM.') !== false) $hasTUM = true;
            }
            if ($hasPTG && $hasTUM) $itemsWithConflict[] = $item['MaterialCode'];
        }

        // Hitung user_choices yang memang untuk item konflik (pakai trim+strtoupper)
        $confirmed = 0;
        foreach ($itemsWithConflict as $conflictCode) {
            foreach ($userChoices as $choice) {
                if (trim(strtoupper($choice['material_code'])) === trim(strtoupper($conflictCode))) {
                    $confirmed++;
                    break;
                }
            }
        }

        // Debug log
        Log::info('DEBUG PTG/TUM: itemsWithConflict', $itemsWithConflict);
        Log::info('DEBUG PTG/TUM: userChoices', $userChoices);
        Log::info('DEBUG PTG/TUM: confirmed', [$confirmed]);
        Log::info('DEBUG PTG/TUM: total_conflict', [count($itemsWithConflict)]);

        // Jika jumlah konfirmasi belum sama dengan jumlah item konflik, tampilkan modal untuk item berikutnya
        if ($confirmed < count($itemsWithConflict)) {
            // Cari item yang belum dikonfirmasi
            foreach ($itemsWithConflict as $conflictCode) {
                $found = false;
                foreach ($userChoices as $choice) {
                    if (trim(strtoupper($choice['material_code'])) === trim(strtoupper($conflictCode))) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    // Debug log
                    Log::info('DEBUG PTG/TUM: Akan tampilkan modal untuk', ['material_code' => $conflictCode]);
                    // Tampilkan modal untuk item ini
                    foreach ($data as $item) {
                        if (trim(strtoupper($item['MaterialCode'])) === trim(strtoupper($conflictCode))) {
                            $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                                ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                                ->get();
                            $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];
                            $prosesData = [];
                            foreach ($masterBomHeader as $bom) {
                                $formula = $bom->Formula;
                                $proses = '';
                                if (strpos($formula, '.PTG.') !== false) $proses = 'PTG';
                                elseif (strpos($formula, '.TUM.') !== false) $proses = 'TUM';
                                if ($proses) {
                                    $prosesData[] = [
                                        'proses' => $proses,
                                        'formula' => $formula,
                                        'material_code' => $bom->MaterialCode,
                                        'machine' => '',
                                        'estimation' => 0
                                    ];
                                }
                            }
                            return response()->json([
                                'success' => false,
                                'message' => 'Terdapat proses PTG dan TUM pada item ' . $item['MaterialCode'] . ', silakan pilih opsi:',
                                'options' => [
                                    'ptg' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'PTG')),
                                    'tum' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'TUM')),
                                    'choices' => ['PTG saja', 'TUM saja', 'PTG dan TUM']
                                ],
                                'item' => $item,
                                'choice_values' => ['ptg_only', 'tum_only', 'both'],
                                'all_conflicts' => $itemsWithConflict
                            ]);
                        }
                    }
                }
            }
        }
        // Jika sudah semua, lanjut proses normal

        $unresolvedItems = [];
        foreach ($itemsWithConflict as $conflictCode) {
            $found = false;
            foreach ($userChoices as $choice) {
                if ($choice['material_code'] === $conflictCode) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $unresolvedItems[] = $conflictCode;
            }
        }

        if (!empty($unresolvedItems)) {
            $firstUnresolved = $unresolvedItems[0];
            foreach ($data as $item) {
                if ($item['MaterialCode'] === $firstUnresolved) {
                    $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                        ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                        ->get();
                    $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];
                    $prosesData = [];
                    foreach ($masterBomHeader as $bom) {
                        $formula = $bom->Formula;
                        $proses = '';
                        if (strpos($formula, '.PTG.') !== false) {
                            $proses = 'PTG';
                        } elseif (strpos($formula, '.TUM.') !== false) {
                            $proses = 'TUM';
                        }
                        if ($proses) {
                            $prosesData[] = [
                                'proses' => $proses,
                                'formula' => $formula,
                                'material_code' => $bom->MaterialCode,
                                'machine' => '',
                                'estimation' => 0
                            ];
                        }
                    }
                    return response()->json([
                        'success' => false,
                        'message' => 'Terdapat proses PTG dan TUM pada item ' . $item['MaterialCode'] . ', silakan pilih opsi:',
                        'options' => [
                            'ptg' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'PTG')),
                            'tum' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'TUM')),
                            'choices' => ['PTG saja', 'TUM saja', 'PTG dan TUM']
                        ],
                        'item' => $item,
                        'choice_values' => ['ptg_only', 'tum_only', 'both'],
                        'all_conflicts' => $itemsWithConflict
                    ]);
                }
            }
        }
        // Jika sudah semua, lanjut proses normal

        foreach ($data as $item) {
            $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                ->get();

            $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];
            $prosesData = [];
            $hasPTG = false;
            $hasTUM = false;
            foreach ($masterBomHeader as $bom) {
                $formula = $bom->Formula;
                if (strpos($formula, '.PTG.') !== false) $hasPTG = true;
                if (strpos($formula, '.TUM.') !== false) $hasTUM = true;
                $proses = '';
                $acuan = '';
                $machineProses = '';
                $estimation = 0;
                $mesin = '';

                if (strpos($formula, '.CTK.') !== false) {
                    $proses = 'CTK';
                    $acuan = 'Cetak';

                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();

                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_ctk . '%')
                            ->first();

                        $mesin = $detailMachine->Code;
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_ctk . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }

                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                } elseif (strpos($formula, '.EPL.') !== false) {
                    $proses = 'EPL';
                    $acuan = 'Emboss / Plong';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_epl . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_epl . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.EMB.') !== false) {
                    $proses = 'EMB';
                    $acuan = 'Emboss';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_emb . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_emb . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.HP.') !== false) {
                    $proses = 'HP';
                    $acuan = 'Hot Print';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_hp . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_hp . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.KPS.') !== false) {
                    $proses = 'KPS';
                    $acuan = 'Kupas';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_kps . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_kps . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.STR.') !== false) {
                    $proses = 'STR';
                    $acuan = 'Sortir';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_str . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_str . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.UV.') !== false) {
                    $proses = 'UV';
                    $acuan = 'UV';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_uv . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_uv . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.STU.') !== false) {
                    $proses = 'STU';
                    $acuan = 'Sortir Ulang';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_stu . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_str . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.PLG.') !== false) {
                    $proses = 'PLG';
                    $acuan = 'Plong';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_plg . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_plg . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                } elseif (strpos($formula, '.PTG.') !== false) {
                    $proses = 'PTG';
                    $acuan = 'Potong';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_ptg . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_ptg . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                    // Tambahkan filter agar hanya satu PTG
                    if (in_array('PTG', array_column($prosesData, 'proses'))) {
                        continue;
                    }
                } elseif (strpos($formula, '.TUM.') !== false) {
                    $proses = 'TUM';
                    $acuan = 'Tumpuk';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_tum . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_tum . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                    // Tambahkan filter agar hanya satu TUM
                    if (in_array('TUM', array_column($prosesData, 'proses'))) {
                        continue;
                    }
                } else {
                    $proses = 'LEM';
                    $acuan = 'Lem';
                    $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                    if ($machineProses) {
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $machineProses->m_lem . '%')
                            ->first();
                    } else {
                        $found = false;

                        for ($i = strlen($item['MaterialCode']) - 1; $i >= 7; $i--) {
                            $prefix = substr($item['MaterialCode'], 0, $i);
                            $mappingItem = MappingItem::where('kode', 'like', $prefix . '%')->first();
                            if ($mappingItem) {
                                $found = true;
                                break;
                            }
                        }
                        $detailMachine = DB::connection('mysql3')
                            ->table('mastermachine')
                            ->where('Code', 'like', '%' . $mappingItem->m_lem . '%')
                            ->first();
                        $mesin = $detailMachine->Code;
                    }
                    $estimation = $quantity / $detailMachine->CapacityPerHour;
                    $mesin = $detailMachine->Code;
                }

                $prosesData[] = [
                    'proses' => $proses,
                    'acuan' => $acuan,
                    'formula' => $formula,
                    'material_code' => $bom->MaterialCode,
                    'machine' => $mesin,
                    'estimation' => $estimation
                ];
            }
            if ($hasPTG && $hasTUM) $itemsWithConflict[] = $item['MaterialCode'];


            // Setelah prosesData terisi, cek jika ada TUM dan PTG sekaligus
            // Cek apakah user sudah memilih untuk item ini
            $userChoice = null;
            foreach ($userChoices as $choice) {
                if ($choice['material_code'] === $item['MaterialCode']) {
                    $userChoice = $choice['choice']; // 'ptg_only', 'tum_only', atau 'both'
                    break;
                }
            }

            // Debug: Log untuk item ini
            Log::info('Processing item: ' . $item['MaterialCode'] . ', User choice: ' . ($userChoice ?? 'none'));

            // Jika belum ada pilihan user, cek konflik
            if (!$userChoice) {
                $hasPTG = in_array('PTG', array_column($prosesData, 'proses'));
                $hasTUM = in_array('TUM', array_column($prosesData, 'proses'));

                if ($hasPTG && $hasTUM) {
                    Log::info('Found conflict for item: ' . $item['MaterialCode']);
                    $conflictItems[] = [
                        'item' => $item,
                        'prosesData' => $prosesData
                    ];
                }
            } else {
                Log::info('User already chose for item: ' . $item['MaterialCode'] . ' - choice: ' . $userChoice);
                // Pastikan item yang sudah dipilih tidak masuk ke conflict items
                $conflictItems = array_filter($conflictItems, function ($conflict) use ($item) {
                    return $conflict['item']['MaterialCode'] !== $item['MaterialCode'];
                });
            }

            // Simpan item yang sudah diproses
            $processedItems[] = $item['MaterialCode'];

            // Jika ada pilihan user, filter proses sesuai pilihan
            if ($userChoice) {
                $originalCount = count($prosesData);
                Log::info('Filtering processes for item: ' . $item['MaterialCode'] . ' with choice: ' . $userChoice);

                if ($userChoice === 'ptg_only') {
                    $prosesData = array_filter($prosesData, function ($proses) {
                        return $proses['proses'] !== 'TUM';
                    });
                    Log::info('Removed TUM processes. Original: ' . $originalCount . ', After: ' . count($prosesData));
                } elseif ($userChoice === 'tum_only') {
                    $prosesData = array_filter($prosesData, function ($proses) {
                        return $proses['proses'] !== 'PTG';
                    });
                    Log::info('Removed PTG processes. Original: ' . $originalCount . ', After: ' . count($prosesData));
                }
                // Jika 'both', gunakan semua proses (tidak perlu filter)
            }

            // Setelah filtering, cek lagi apakah masih ada konflik
            if ($userChoice) {
                $hasPTG = in_array('PTG', array_column($prosesData, 'proses'));
                $hasTUM = in_array('TUM', array_column($prosesData, 'proses'));

                // Jika masih ada konflik, berarti ada masalah dengan pilihan user
                if ($hasPTG && $hasTUM) {
                    // Log error atau handle sesuai kebutuhan
                    Log::warning("Masih ada konflik PTG/TUM setelah filtering untuk item: " . $item['MaterialCode']);
                }
            }

            // dd($prosesData);

            $detailProsesItem[$item['WODocNo']] = [
                'SODocNo' => $item['SODocNo'],
                'WODocNo' => $item['WODocNo'],
                'MaterialCode' => $item['MaterialCode'],
                'MaterialName' => $item['Name'],
                'Unit' => $item['Unit'],
                'Quantity' => $item['Quantity'],
                'Up' => $item['Up'],
                'DeliveryDate' => $item['DeliveryDate'],
                'Status' => $item['Status'],
                'processes' => $prosesData
            ];
        }

        // dd('ini detailProsesItem', $detailProsesItem);



        // Jika ada item yang konflik, return response untuk pilihan user

        // Jika masih ada item konflik yang belum dipilih user, tampilkan modal untuk satu item saja
        if (!empty($unresolvedItems)) {
            // Cari data item dan prosesData untuk item pertama yang belum dipilih
            $firstUnresolved = $unresolvedItems[0];
            foreach ($data as $item) {
                if ($item['MaterialCode'] === $firstUnresolved) {
                    $masterBomHeader = DB::connection('mysql3')->table('masterbomh')
                        ->where('MaterialCode', 'like', '%' . $item['MaterialCode'] . '%')
                        ->get();
                    $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];
                    $prosesData = [];
                    foreach ($masterBomHeader as $bom) {
                        $formula = $bom->Formula;
                        $proses = '';
                        if (strpos($formula, '.PTG.') !== false) {
                            $proses = 'PTG';
                        } elseif (strpos($formula, '.TUM.') !== false) {
                            $proses = 'TUM';
                        }
                        if ($proses) {
                            $prosesData[] = [
                                'proses' => $proses,
                                'formula' => $formula,
                                'material_code' => $bom->MaterialCode,
                                'machine' => '',
                                'estimation' => 0
                            ];
                        }
                    }
                    return response()->json([
                        'success' => false,
                        'message' => 'Terdapat proses PTG dan TUM pada item ' . $item['MaterialCode'] . ', silakan pilih opsi:',
                        'options' => [
                            'ptg' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'PTG')),
                            'tum' => array_values(array_filter($prosesData, fn($p) => $p['proses'] === 'TUM')),
                            'choices' => ['PTG saja', 'TUM saja', 'PTG dan TUM']
                        ],
                        'item' => $item,
                        'choice_values' => ['ptg_only', 'tum_only', 'both'],
                        'all_conflicts' => $itemsWithConflict
                    ]);
                }
            }
        }

        // dd($detailProsesItem); // Disabled for testing

        $prosesGrouped = [];

        foreach ($detailProsesItem as $materialCode => $item) {
            // dd($item);
            if (isset($item['processes']) && is_array($item['processes'])) {
                foreach ($item['processes'] as $proses) {
                    $namaProses = $proses['proses'];

                    if (!isset($prosesGrouped[$namaProses])) {
                        $prosesGrouped[$namaProses] = [];
                    }

                    $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];

                    $dataProses = [
                        'MaterialCode' => $materialCode,
                        'CodeItem' => $item['MaterialCode'],
                        'MaterialName' => $item['MaterialName'],
                        'Machine' => $proses['machine'],
                        'Estimation' => $proses['estimation'],
                        'Formula' => $proses['formula'],
                        'Quantity' => $quantity,
                        'DeliveryDate' => $item['DeliveryDate'],
                        'StartJam' => '',
                        'EndJam' => '',
                    ];

                    if ($namaProses == 'CTK') {
                        $jmlWarna = MappingItem::where('kode', $materialCode)->pluck('jumlah_warna')->first();
                        $warnaDetail = MappingItem::where('kode', $materialCode)->first();

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

        // dd($prosesGrouped);

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

        // dd($detailProsesItem);
        // FIX: Gunakan key yang sama dengan submitPlanFirst (MaterialCode_WODocNo)
        foreach ($detailProsesItem as $uniqueKey => $item) {
            // dd($uniqueKey);
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
                $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $machine)->first();
                $capacityPerHour = $machineData && isset($machineData->CapacityPerHour) ? $machineData->CapacityPerHour : 1;
                $unitMachine = $machineData && isset($machineData->Unit) ? $machineData->Unit : 'PCS';
                $quantity = $item['Quantity'];

                // // Jika unit mesin dan unit plan berbeda, bagi quantity dengan Up
                // if ($unitMachine !== $item['Unit']) {
                //     $quantity = $quantity / $item['Up'];
                // }

                $estimation = $quantity / $capacityPerHour;
                $estSeconds = $estimation * 3600;
                $start = clone $prevEnd;
                $end = (clone $start)->addSeconds($estSeconds);
                $setup = 0;
                $break = 0;

                // FIX: Gunakan uniqueKey (MaterialCode_WODocNo) sebagai key untuk planPerItem
                $planPerItem[$uniqueKey][] = [
                    'MaterialCode' => $materialCode,
                    'MaterialName' => $item['MaterialName'],
                    'CodeItem' => $item['MaterialCode'],
                    'Machine' => $machine,
                    'Proses' => $proses['proses'],
                    'StartJam' => $start->format('Y-m-d H:i:s'),
                    'EndJam' => $end->format('Y-m-d H:i:s'),
                    'SetupSeconds' => $setup,
                    'BreakSeconds' => $break,
                    'Estimation' => $estimation,
                    'Quantity' => $quantity,
                    'Up' => $item['Up'],
                    'Unit' => $item['Unit'],
                    'Formula' => $proses['formula'],
                    'SODocNo' => $item['SODocNo'] ?? null,
                    'WODocNo' => $item['WODocNo'] ?? null,
                ];
                $prevEnd = $end;
            }
        }

        // dd($planPerItem);

        // Setelah seluruh proses penjadwalan selesai, filter hasil akhir agar hanya ada satu PTG dan satu TUM per item
        foreach ($planPerItem as $materialCode => $plans) {
            $planPerItem[$materialCode] = $this->filterSinglePTGandTUM($plans);
            $planPerItem[$materialCode] = $this->filterUniqueProcesses($planPerItem[$materialCode]);
        }

        // Optimasi penempatan proses CTK berdasarkan kesamaan warna
        $planPerItem = $this->optimizeCTKPlacement($planPerItem, $startDate);

        // dd('planPerItem1', $planPerItem);

        // Kelompokkan berdasarkan proses dan mesin, hitung ulang start_jam_new dan end_jam_new
        $planPerItem = $this->groupAndRecalculateMachineSchedule($planPerItem, $startDate);
        // dd('ini ', $planPerItem);

        // Tampilkan pengelompokkan berdasarkan proses dan mesin
        $groupedByMachine = $this->displayGroupedByMachine($planPerItem);


        // Tampilkan preview sebelum simpan ke database
        Log::info('Sending preview response from submitPlanFirst with data:', [
            'planPerItem_count' => count($planPerItem),
            'groupedByMachine_count' => count($groupedByMachine),
            'planPerItem_keys' => array_keys($planPerItem),
            'sample_data' => array_slice($planPerItem, 0, 2, true) // Ambil 2 sample data
        ]);

        // dd('ini ', $planPerItem);



        if (count($planPerItem) > 0 && count($groupedByMachine) > 0) {
            return response()->json([
                'success' => false,
                'preview' => true,
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
                'message' => 'Preview rencana produksi gagal dibuat',
            ]);
        }
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

                    $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];

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
                $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $machine)->first();
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
            $detailMachine = DB::connection('mysql3')
                ->table('mastermachine')
                ->where('Code', 'like', '%' . $machine->m_ctk . '%')
                ->first();
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

    public function getPlanFirstData()
    {
        $data = PlanFirstProduction::with('machine')
            ->whereNotNull('start_jam')
            ->whereNotNull('end_jam')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'code_plan' => $plan->code_plan,
                    'code_machine' => $plan->code_machine,
                    'code_item' => $plan->code_item,
                    'wo_docno' => $plan->wo_docno,
                    'so_docno' => $plan->so_docno,
                    'quantity' => $plan->quantity,
                    'start_jam' => $plan->start_jam ? date('Y-m-d\TH:i:s', strtotime($plan->start_jam)) : null,
                    'end_jam' => $plan->end_jam ? date('Y-m-d\TH:i:s', strtotime($plan->end_jam)) : null,
                    'est_jam' => $plan->est_jam,
                    'flag_status' => $plan->flag_status,
                    'process' => $plan->process,
                ];
            });

        Log::info('Plan First Data:', ['data' => $data->toArray()]);

        return response()->json([
            'status' => 'success',
            'data' => $data
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
        $machines = Machine::where('Description', '!=', 'JANGAN DIPAKAI')
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
        $machines = Machine::where('Description','!=', '%JANGAN DIPAKAI%')
            ->where('Department', 'like', '%CTK%')
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
        $quantity = $item['Quantity'] <= 500 ? 500 : $item['Quantity'];
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
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_ctk . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.EPL.') !== false) {
                $proses = 'EPL';
                $acuan = 'Emboss / Plong';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_epl . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.EMB.') !== false) {
                $proses = 'EMB';
                $acuan = 'Emboss';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_emb . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.HP.') !== false) {
                $proses = 'HP';
                $acuan = 'Hot Print';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_hp . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.KPS.') !== false) {
                $proses = 'KPS';
                $acuan = 'Kupas';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_kps . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.STR.') !== false) {
                $proses = 'STR';
                $acuan = 'Sortir';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_str . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.UV.') !== false) {
                $proses = 'UV';
                $acuan = 'UV';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_uv . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.STU.') !== false) {
                $proses = 'STU';
                $acuan = 'Sortir Ulang';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_stu . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.PLG.') !== false) {
                $proses = 'PLG';
                $acuan = 'Plong';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_plg . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.PTG.') !== false) {
                $proses = 'PTG';
                $acuan = 'Potong';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_ptg . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } elseif (strpos($formula, '.TUM.') !== false) {
                $proses = 'TUM';
                $acuan = 'Tumpuk';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_tum . '%')
                        ->first();
                    $mesin = $detailMachine->Code ?? '';
                    $estimation = $quantity / ($detailMachine->CapacityPerHour ?? 1);
                }
            } else {
                $proses = 'LEM';
                $acuan = 'Lem';
                $machineProses = MappingItem::where('kode', $item['MaterialCode'])->first();
                if ($machineProses) {
                    $detailMachine = DB::connection('mysql3')
                        ->table('mastermachine')
                        ->where('Code', 'like', '%' . $machineProses->m_lem . '%')
                        ->first();
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
            $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
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

            $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
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
                $machineData = DB::connection('mysql3')->table('mastermachine')->where('Code', $plan['Machine'])->first();
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
}
