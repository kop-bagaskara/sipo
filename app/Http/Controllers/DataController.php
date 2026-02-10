<?php

namespace App\Http\Controllers;

use App\Models\PlanContinuedProduction;
use App\Models\PlanFirstProduction;
use App\Models\PlanChangeHistory;
use App\Models\PlanProduction;
use App\Models\SeriesMaterial;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DataController extends Controller
{

    public function indexFirstPlanDataResult()
    {
        return view('main.data.data-first-plan');
    }

    public function indexPlanHarianDataResult()
    {
        return view('main.data.data-continued-plan');
    }

    public function indexFirstPlanDataResults(Request $request)
    {
        if ($request->ajax()) {

            $data = PlanFirstProduction::where('status_proses', '!=', 'MASUK PLAN HARIAN')->get();
            $gData = $data->groupBy('code_plan');

            $formattedData = $gData->map(function ($items, $key) {
                return [
                    'code_plan' => $key,
                    'data' => $items,
                ];
            });

            return DataTables::of($formattedData)
                ->addIndexColumn()
                ->addColumn('count_machine', function ($data_a) {
                    $machineCodes = $data_a['data']->pluck('code_machine')->flatten();
                    $machineCount = count(array_unique(explode(',', $machineCodes->implode(','))));
                    return $machineCount;
                })
                ->addColumn('process', function ($data_a) {
                    return $data_a['data']->first()->process;
                })
                ->addColumn('created_by', function ($data_a) {
                    return $data_a['data']->first()->created_by;
                })
                ->addColumn('created_at', function ($data_a) {
                    // Format tanggal
                    return $data_a['data']->first()->created_at->format('d-m-Y');
                })
                ->addColumn('action', function ($data_a) {
                    // Buat tombol aksi untuk setiap kelompok data
                    $btn = '<button id="edit-data" data-shift="" data-id="' . $data_a['data']->first()->code_plan . '" class="btn btn-primary btn-sm edit-data text-light" data-original-title="Edit" target="_blank"><i class="mdi mdi-eye"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function indexPlanHarianDataResults(Request $request)
    {

        if ($request->ajax()) {
            $data = PlanContinuedProduction::all();

            $byDateGroups = $data->groupBy('date_plan');

            $formattedData = [];

            foreach ($byDateGroups as $date => $dateItems) {
                $byMachine = $dateItems->groupBy('code_machine');

                $machineDetails = [];
                foreach ($byMachine as $machine => $items) {
                    $processes = $items->pluck('process')->unique()->filter()->toArray();
                    $creators = $items->pluck('created_by')->unique()->filter()->toArray();
                    $latestUpdate = $items->max('updated_at');



                    $itemDetails = $items->map(function ($item) {
                        $series = SeriesMaterial::where('department', $item->department)
                            ->where('proses', $item->process)
                            ->get();

                        $series_names = $series->pluck('series_material')->filter()->join(',');

                        // Buat array untuk menyimpan job orders
                        $jobOrders = [];

                        if ($series_names) {
                            // Split series names jika ada multiple
                            $seriesArray = explode(',', $series_names);

                            foreach ($seriesArray as $seriesName) {
                                // Buat material code dengan format yang diinginkan
                                $materialCode = $item->code_item . '.' . trim($seriesName);

                                // Cari di job order
                                $jobOrder = DB::connection('mysql3')
                                    ->table('joborder')
                                    ->where('materialCode', $materialCode)
                                    ->first();

                                // dd($jobOrder);

                                if ($jobOrder) {
                                    $jobOrders[] = $jobOrder->DocNo;
                                }
                            }
                        }

                        return [
                            'code_plan' => $item->code_plan,
                            'id_plan_harian' => $item->id,
                            'code_item' => $item->code_item,
                            'material_name' => $item->material_name,
                            'qty_plan' => $item->quantity,
                            'process' => $item->process,
                            'start_time' => $item->start_jam,
                            'end_time' => $item->end_jam,
                            'setup_time' => $item->setup,
                            'istirahat_time' => $item->istirahat,
                            'created_by' => $item->created_by,
                            'created_at' => $item->created_at ? date('d-m-Y', strtotime($item->created_at)) : '',
                            'updated_at' => $item->updated_at ? date('d-m-Y', strtotime($item->updated_at)) : '',
                            'wo_docno' => $item->wo_docno,
                            'so_docno' => $item->so_docno,
                            'capacity' => $item->capacity,
                            'joborder_docno' => implode(',', $jobOrders), // Gabungkan semua job order dengan koma
                            'series_material' => $series_names // Tambahkan series material untuk referensi
                        ];
                    })->toArray();


                    $machineDetails[] = [
                        'machine' => $machine,
                        'process' => implode(', ', $processes),
                        'created_by' => implode(', ', $creators),
                        'updated_at' => $latestUpdate ? date('d-m-Y', strtotime($latestUpdate)) : '',
                        'item_count' => $items->count(),
                        'items' => $itemDetails
                    ];
                }

                $formattedData[] = [
                    'date' => $date ?: 'No Date',
                    'machine_count' => count($byMachine),
                    'total_items' => $dateItems->count(),
                    'machines' => $machineDetails,

                    'machines_html' => view('main.data.partials.machine-details', [
                        'machines' => $machineDetails,
                        'date' => $date
                    ])->render()
                ];
            }

            // Sort by date
            $formattedData = collect($formattedData)->sortBy('date')->values()->all();

            return response()->json([
                'data' => $formattedData
            ]);
        }
    }

    public function changePlanDate(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'plan_id' => 'required',
                'new_date' => 'required|date',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
                'new_machine' => 'required_if:transfer_machine,true'
            ]);

            $newStartTime = Carbon::parse($request->start_time);
            $newEndTime = Carbon::parse($request->end_time);
            $targetDate = Carbon::parse($request->start_time)->format('Y-m-d');

            // dd($targetDate, $request->plan_id, $request->new_machine);
            // Ambil semua plan di mesin itu pada tanggal target dan setelahnya
            $plansOnDate = PlanContinuedProduction::where('code_machine', $request->new_machine)
                ->whereDate('date_plan', '>=', $targetDate)
                ->where('id', '!=', $request->plan_id)
                ->orderBy('date_plan')
                ->orderBy('start_jam')
                ->get();

            // dd($plansOnDate);

            if ($plansOnDate->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada plan lain di tanggal tersebut dan setelahnya'
                ]);
            }

            // Cari plan yang tabrakan hanya pada tanggal yang sama
            $conflictingPlan = null;
            foreach ($plansOnDate as $plan) {
                $planStart = Carbon::parse($plan->start_jam);
                $planEnd = Carbon::parse($plan->end_jam);
                $planDate = Carbon::parse($plan->date_plan)->format('Y-m-d');

                if ($planDate === $targetDate) {
                    if (
                        ($newStartTime->between($planStart, $planEnd)) ||
                        ($newEndTime->between($planStart, $planEnd)) ||
                        ($planStart->between($newStartTime, $newEndTime))
                    ) {
                        $conflictingPlan = $plan;
                        break;
                    }
                }
            }

            if ($conflictingPlan) {
                $conflictMessage = 'Waktu yang dipilih bertabrakan dengan plan yang sudah ada di mesin ' . $request->new_machine . ' pada tanggal ' . $targetDate . ':<br><br>';
                $conflictMessage .= '<strong>Plan yang akan tergeser:</strong><br>';

                $plansToBeShifted = $plansOnDate->filter(function ($p) use ($conflictingPlan) {
                    return (
                        Carbon::parse($p->date_plan)->gt(Carbon::parse($conflictingPlan->date_plan)) ||
                        (
                            Carbon::parse($p->date_plan)->eq(Carbon::parse($conflictingPlan->date_plan)) &&
                            Carbon::parse($p->start_jam)->gte(Carbon::parse($conflictingPlan->start_jam))
                        )
                    );
                });

                foreach ($plansToBeShifted as $plan) {
                    $conflictMessage .= '- ' . $plan->material_name . ' (' .
                        Carbon::parse($plan->date_plan)->format('Y-m-d') . ' ' .
                        Carbon::parse($plan->start_jam)->format('H:i') . ' - ' .
                        Carbon::parse($plan->end_jam)->format('H:i') . ')<br>';
                }

                return response()->json([
                    'success' => false,
                    'message' => $conflictMessage
                ]);
            }

            // dd($conflictingPlan);

            // Jika tidak ada yang tabrakan
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada tabrakan dengan plan yang ada'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function geserPlan(Request $request)
    {
        // dd($request->all());
        // Ambil data plan baru dari request
        $newStartTime = Carbon::parse($request->input('start_time'));
        $newEndTime = Carbon::parse($request->input('end_time'));
        $targetDate = Carbon::parse($request->input('start_time'))->format('Y-m-d');
        $newMachine = $request->input('new_machine');

        // Tampilkan semua item di mesin baru
        $allItemsInNewMachine = PlanContinuedProduction::where('code_machine', $newMachine)
            ->orderBy('date_plan')
            ->orderBy('start_jam')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'material_name' => $item->material_name,
                    'date_plan' => $item->date_plan,
                    'start_jam' => $item->start_jam,
                    'end_jam' => $item->end_jam,
                    'quantity' => $item->quantity,
                    'capacity' => $item->capacity,
                    'setup' => $item->setup,
                    'istirahat' => $item->istirahat
                ];
            });

        // dd([
        //     'new_machine' => $newMachine,
        //     'all_items_in_new_machine' => $allItemsInNewMachine,
        //     'request_data' => $request->all()
        // ]);

        // Ambil semua plan di mesin itu pada tanggal target dan setelahnya
        $plansOnDate = PlanContinuedProduction::where('code_machine', $newMachine)
            ->whereDate('date_plan', '>=', $targetDate)
            ->where('id', '!=', $request->input('plan_id'))
            ->orderBy('date_plan')
            ->orderBy('start_jam')
            ->get();

        // dd($plansOnDate);

        // Cari plan yang tabrakan hanya pada tanggal yang sama
        $conflictingPlan = null;
        foreach ($plansOnDate as $plan) {
            $planStart = Carbon::parse($plan->start_jam);
            $planEnd = Carbon::parse($plan->end_jam);
            $planDate = Carbon::parse($plan->date_plan)->format('Y-m-d');

            if ($planDate === $targetDate) {
                if (
                    ($newStartTime->between($planStart, $planEnd)) ||
                    ($newEndTime->between($planStart, $planEnd)) ||
                    ($planStart->between($newStartTime, $newEndTime))
                ) {
                    $conflictingPlan = $plan;
                    break;
                }
            }
        }

        // dd($conflictingPlan);

        // Seleksi plan yang akan tergeser
        $plansToBeShifted = collect();

        if ($conflictingPlan) {
            $plansToBeShifted = $plansOnDate->filter(function ($p) use ($conflictingPlan) {
                return (
                    Carbon::parse($p->date_plan)->gt(Carbon::parse($conflictingPlan->date_plan)) ||
                    (
                        Carbon::parse($p->date_plan)->eq(Carbon::parse($conflictingPlan->date_plan)) &&
                        Carbon::parse($p->start_jam)->gte(Carbon::parse($conflictingPlan->start_jam))
                    )
                );
            });
        }

        // dd($plansToBeShifted);

        // dd hasil seleksi
        $crashItem = null;
        if ($conflictingPlan) {
            $previousPlan = PlanContinuedProduction::where('code_machine', $newMachine)
                ->where('start_jam', '<', $conflictingPlan->start_jam)
                ->where('id', '!=', $request->input('plan_id'))
                ->orderBy('start_jam', 'desc')
                ->first();

            // dd($previousPlan);
            if ($previousPlan) {
                $crashItem = [
                    'id' => $previousPlan->id,
                    'id_first_plan' => $previousPlan->id_first_plan,
                    'code_plan' => $previousPlan->code_plan,
                    'code_item' => $previousPlan->code_item,
                    'code_machine' => $previousPlan->code_machine,
                    'qty' => $previousPlan->quantity,
                    'up_cetak' => $previousPlan->up_cetak,
                    'capacity' => $previousPlan->capacity,
                    'est_jam' => $previousPlan->est_jam,
                    'est_day' => $previousPlan->est_day,
                    'start' => $previousPlan->start_jam,
                    'end' => $previousPlan->end_jam,
                    'setup' => $previousPlan->setup,
                    'istirahat' => $previousPlan->istirahat,
                    'flag_status' => $previousPlan->flag_status,
                    'wo_docno' => $previousPlan->wo_docno,
                    'so_docno' => $previousPlan->so_docno,
                    'delivery_date' => $previousPlan->delivery_date,
                    'shift_msn' => $previousPlan->shift_msn,
                    'status_proses' => $previousPlan->status_proses,
                    'proses' => $previousPlan->process,
                    'department' => $previousPlan->department,
                    'material_name' => $previousPlan->material_name,
                    'date_plan' => $previousPlan->date_plan,

                ];
            }
        }
        // dd($crashItem);

        $shiftedItems = [];
        foreach ($plansToBeShifted as $plan) {
            $shiftedItems[] = [
                'id' => $plan->id,
                'id_first_plan' => $plan->id_first_plan,
                'code_plan' => $plan->code_plan,
                'code_item' => $plan->code_item,
                'code_machine' => $plan->code_machine,
                'qty' => $plan->quantity,
                'up_cetak' => $plan->up_cetak,
                'capacity' => $plan->capacity,
                'est_jam' => $plan->est_jam,
                'est_day' => $plan->est_day,
                'start' => $plan->start_jam,
                'end' => $plan->end_jam,
                'setup' => $plan->setup,
                'istirahat' => $plan->istirahat,
                'flag_status' => $plan->flag_status,
                'wo_docno' => $plan->wo_docno,
                'so_docno' => $plan->so_docno,
                'delivery_date' => $plan->delivery_date,
                'shift_msn' => $plan->shift_msn,
                'status_proses' => $plan->status_proses,
                'proses' => $plan->process,
                'department' => $plan->department,
                'material_name' => $plan->material_name,
                'date_plan' => $plan->date_plan,
            ];
        }


        // dd([
        //     'crash_item' => $crashItem,
        //     'shifted_items' => $shiftedItems,
        //     'request_all' => $request->all(),
        // ]);
        // dd($request->all());
        $setup = (float) $request->input('setup');
        $istirahat = (float) $request->input('istirahat');
        $quantity = (float) $request->input('qty');
        // dd($quantity);
        $capacity = (float) $request->input('capacity');

        $startTime = Carbon::parse($crashItem['end']);
        $estimationHours = $quantity / $capacity;
        $endTime = $startTime->copy()->addHours($estimationHours + $setup + $istirahat);

        $finalDataDetailPlan = PlanContinuedProduction::find($request->input('plan_id'));
        // dd($finalDataDetailPlan);
        $finalPlan = [
            [
                'id' => $request->input('plan_id'),
                'id_first_plan' => $finalDataDetailPlan->id_first_plan,
                'code_plan' => $finalDataDetailPlan->code_plan,
                'code_item' => $finalDataDetailPlan->code_item,
                'code_machine' => $finalDataDetailPlan->code_machine,
                'qty' => $finalDataDetailPlan->quantity,
                'up_cetak' => $finalDataDetailPlan->up_cetak,
                'capacity' => $finalDataDetailPlan->capacity,
                'est_jam' => $finalDataDetailPlan->est_jam,
                'est_day' => $finalDataDetailPlan->est_day,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $endTime->format('Y-m-d H:i:s'),
                'qty' => $quantity,
                'capacity' => $capacity,
                'setup' => intval($setup),
                'istirahat' => intval($istirahat),
                'flag_status' => $finalDataDetailPlan->flag_status,
                'wo_docno' => $finalDataDetailPlan->wo_docno,
                'so_docno' => $finalDataDetailPlan->so_docno,
                'delivery_date' => $finalDataDetailPlan->delivery_date,
                'shift_msn' => $finalDataDetailPlan->shift_msn,
                'status_proses' => $finalDataDetailPlan->status_proses,
                'proses' => $finalDataDetailPlan->process,
                'department' => $finalDataDetailPlan->department,
                'material_name' => $finalDataDetailPlan->material_name,
                'date_plan' => $finalDataDetailPlan->date_plan,
            ]
        ];

        // dd($shiftedItems);

        // Perbarui shifted_items
        foreach ($shiftedItems as $index => $item) {
            // dd($item);
            $itemStartTime = Carbon::parse($finalPlan[$index]['end']);
            $itemQuantity = (float) $item['qty'];
            $itemCapacity = (float) $item['capacity'];
            $itemEstimationHours = $itemQuantity / $itemCapacity;

            $itemEndTime = $itemStartTime->copy()->addHours(
                $itemEstimationHours + $item['setup'] + ($item['istirahat'] > 0 ? $item['istirahat'] : 0)
            );

            $finalPlan[] = [
                'id' => $item['id'],
                'id_first_plan' => $item['id_first_plan'],
                'code_plan' => $item['code_plan'],
                'code_item' => $item['code_item'],
                'code_machine' => $item['code_machine'],
                'qty' => $item['qty'],
                'up_cetak' => $item['up_cetak'],
                'capacity' => $item['capacity'],
                'est_jam' => $item['est_jam'],
                'est_day' => $item['est_day'],
                'start' => $itemStartTime->format('Y-m-d H:i:s'),
                'end' => $itemEndTime->format('Y-m-d H:i:s'),
                'qty' => $itemQuantity,
                'capacity' => $itemCapacity,
                'setup' => intval($item['setup']),
                'istirahat' => intval($item['istirahat']),
                'flag_status' => $item['flag_status'],
                'wo_docno' => $item['wo_docno'],
                'so_docno' => $item['so_docno'],
                'delivery_date' => $item['delivery_date'],
                'shift_msn' => $item['shift_msn'],
                'status_proses' => $item['status_proses'],
                'proses' => $item['proses'],
                'department' => $item['department'],
                'material_name' => $item['material_name'],
                'date_plan' => $item['date_plan'],
            ];
        }
        // dd($shiftedItems);

        $currentMachine = $request->input('current_machine');
        $itemName = $request->input('item_name');

        $plans = PlanContinuedProduction::where('code_machine', $currentMachine)->orderBy('start_jam')->get();

        // dd($plans);

        $movedItemIndex = $plans->search(function ($plan) use ($itemName) {
            return $plan->material_name === $itemName;
        });

        if ($movedItemIndex === false) {
            return response()->json([
                'success' => false,
                'message' => 'Item yang dipindahkan tidak ditemukan di mesin asli.'
            ]);
        }

        // Ambil item sebelum dan sesudah item yang dipindahkan
        $previousItem = $plans->get($movedItemIndex - 1);
        $nextItems = $plans->slice($movedItemIndex + 1);

        // Perbarui susunan plan
        $updatedPlans = [];
        $previousEndTime = $previousItem ? Carbon::parse($previousItem->end_jam) : null;

        // dd($nextItems);
        foreach ($nextItems as $index => $item) {
            // dd($index);
            // Jika item adalah indeks pertama (index 0), set start jam ke 08:00 pada current_date
            if ($movedItemIndex === 0 && !$previousEndTime) {
                $startTime = Carbon::parse($request->input('current_date'))->setTime(8, 0);
            } else {
                $startTime = $previousEndTime ?: Carbon::parse($item->start_jam);
            }

            // dd($startTime);

            $quantity = (float) $item->quantity;
            $capacity = (float) $item->capacity;
            $setup = (float) $item->setup;
            $istirahat = (float) $item->istirahat;
            $machine = $item->code_machine;

            $estimationHours = $quantity / $capacity;
            $endTime = $startTime->copy()->addHours($estimationHours + $setup + $istirahat);

            // Simpan perubahan
            $updatedPlans[] = [
                'id' => $item->id,
                'id_first_plan' => $item->id_first_plan,
                'code_plan' => $item->code_plan,
                'code_item' => $item->code_item,
                'code_machine' => $machine,
                'qty' => $item->quantity,
                'up_cetak' => $item->up_cetak,
                'capacity' => $item->capacity,
                'est_jam' => $item->est_jam,
                'est_day' => $item->est_day,
                'start' => $startTime->format('Y-m-d H:i:s'),
                'end' => $endTime->format('Y-m-d H:i:s'),
                'qty' => $quantity,
                'capacity' => $capacity,
                'setup' => intval($setup),
                'istirahat' => intval($istirahat),
                'flag_status' => $item->flag_status,
                'wo_docno' => $item->wo_docno,
                'so_docno' => $item->so_docno,
                'delivery_date' => $item->delivery_date,
                'shift_msn' => $item->shift_msn,
                'status_proses' => $item->status_proses,
                'proses' => $item->proses,
                'department' => $item->department,
                'material_name' => $item->material_name,
                'date_plan' => $item->date_plan,
                // 'machine' => $machine,
            ];

            // Perbarui previousEndTime untuk item berikutnya
            $previousEndTime = $endTime;
        }

        $currentMachine = $request->input('current_machine');

        // dd($updatedPlans);

        try {
            DB::beginTransaction();

            // Ambil semua item di mesin lama setelah penghapusan
            $remainingItems = PlanContinuedProduction::where('code_machine', $currentMachine)
                ->orderBy('start_jam')
                ->get();

            // dd($remainingItems);

            // Hapus item yang dipindahkan dari mesin lama
            // PlanContinuedProduction::where('id', $request->input('plan_id'))->delete();

            // Format remaining items seperti $allPlans
            $remainingPlans = [];
            $previousEndTime = null;
            foreach ($remainingItems as $item) {
                if ($item->id == $request->input('plan_id')) {
                    continue; // Skip item yang dipindahkan
                }

                if (!$previousEndTime) {
                    // Jika ini item pertama, mulai dari jam 8 pagi
                    $startTime = Carbon::parse($item->date_plan)->setTime(8, 0);
                } else {
                    $startTime = $previousEndTime;
                }

                $quantity = (float) $item->quantity;
                $capacity = (float) $item->capacity;
                $setup = (float) $item->setup;
                $istirahat = (float) $item->istirahat;

                $estimationHours = $quantity / $capacity;
                $endTime = $startTime->copy()->addHours($estimationHours + $setup + $istirahat);

                // Format data seperti $allPlans
                $remainingPlans[] = [
                    'id' => $item->id,
                    'id_first_plan' => $item->id_first_plan,
                    'code_plan' => $item->code_plan,
                    'code_item' => $item->code_item,
                    'code_machine' => $currentMachine,
                    'qty' => $item->quantity,
                    'up_cetak' => $item->up_cetak,
                    'capacity' => $item->capacity,
                    'est_jam' => $item->est_jam,
                    'est_day' => $item->est_day,
                    'start' => $startTime->format('Y-m-d H:i:s'),
                    'end' => $endTime->format('Y-m-d H:i:s'),
                    'qty' => $quantity,
                    'capacity' => $capacity,
                    'setup' => intval($setup),
                    'istirahat' => intval($istirahat),
                    'flag_status' => $item->flag_status,
                    'wo_docno' => $item->wo_docno,
                    'so_docno' => $item->so_docno,
                    'delivery_date' => $item->delivery_date,
                    'shift_msn' => $item->shift_msn,
                    'status_proses' => $item->status_proses,
                    'proses' => $item->proses,
                    'department' => $item->department,
                    'material_name' => $item->material_name,
                    'date_plan' => $item->date_plan,
                ];

                $previousEndTime = $endTime;
            }

            // Proses untuk mesin baru
            $allPlans = array_merge($finalPlan, $updatedPlans);

            // dd([
            //     'remaining_plans' => $remainingPlans,
            //     'all_plans' => $allPlans
            // ]);

            // Hapus semua plan di kedua mesin
            // PlanContinuedProduction::where('code_machine', $currentMachine)->delete();

            // Ambil waktu mulai dari plan pertama di allPlans
            $firstPlanStartTime = collect($allPlans)->first()['start'];

            // $deleted = PlanContinuedProduction::where('code_machine', $newMachine)
            //         ->where('start_jam', '>=', $firstPlanStartTime)
            //         ->get();

                // dd($deleted);

            PlanContinuedProduction::where('code_machine', $newMachine)
                ->where('start_jam', '>=', $firstPlanStartTime)
                ->delete();

            // Create ulang plan untuk mesin lama dari remainingPlans
            foreach ($remainingPlans as $plan) {
                PlanContinuedProduction::create([
                    'id' => $plan['id'],
                    'id_first_plan' => $plan['id_first_plan'],
                    'code_plan' => $plan['code_plan'],
                    'code_item' => $plan['code_item'],
                    'code_machine' => $currentMachine,
                    'qty' => $plan['qty'],
                    'up_cetak' => $plan['up_cetak'],
                    'capacity' => $plan['capacity'],
                    'est_jam' => $plan['est_jam'],
                    'est_day' => $plan['est_day'],
                    'start_jam' => $plan['start'],
                    'end_jam' => $plan['end'],
                    'quantity' => $plan['qty'],
                    'capacity' => $plan['capacity'],
                    'setup' => $plan['setup'],
                    'istirahat' => $plan['istirahat'],
                    'flag_status' => $plan['flag_status'],
                    'wo_docno' => $plan['wo_docno'],
                    'so_docno' => $plan['so_docno'],
                    'delivery_date' => $plan['delivery_date'],
                    'shift_msn' => $plan['shift_msn'],
                    'status_proses' => $plan['status_proses'],
                    'proses' => $plan['proses'],
                    'department' => $plan['department'],
                    'material_name' => $plan['material_name'],
                    'date_plan' => $plan['date_plan'],
                ]);
            }

            // Create ulang plan untuk mesin baru dari allPlans
            foreach ($allPlans as $plan) {
                PlanContinuedProduction::create([
                    'id' => $plan['id'],
                    'id_first_plan' => $plan['id_first_plan'],
                    'code_plan' => $plan['code_plan'],
                    'code_item' => $plan['code_item'],
                    'code_machine' => $plan['machine'] ?? $newMachine,
                    'qty' => $plan['qty'],
                    'up_cetak' => $plan['up_cetak'],
                    'capacity' => $plan['capacity'],
                    'est_jam' => $plan['est_jam'],
                    'est_day' => $plan['est_day'],
                    'start_jam' => $plan['start'],
                    'end_jam' => $plan['end'],
                    'quantity' => $plan['qty'],
                    'capacity' => $plan['capacity'],
                    'setup' => $plan['setup'],
                    'istirahat' => $plan['istirahat'],
                    'flag_status' => $plan['flag_status'],
                    'wo_docno' => $plan['wo_docno'],
                    'so_docno' => $plan['so_docno'],
                    'delivery_date' => $plan['delivery_date'],
                    'shift_msn' => $plan['shift_msn'],
                    'status_proses' => $plan['status_proses'],
                    'proses' => $plan['proses'],
                    'department' => $plan['department'],
                    'material_name' => $plan['material_name'],
                    'date_plan' => $plan['date_plan'],
                ]);
            }

            // Catat riwayat perubahan ke tabel plan_change_histories
            foreach (array_merge($remainingPlans, $allPlans) as $plan) {
                PlanChangeHistory::create([
                    'code_plan' => $plan['code_plan'],
                    'old_date' => $plan['date_plan'],
                    'new_date' => $request->input('new_date'),
                    'old_machine' => $plan['code_machine'],
                    'new_machine' => $request->input('new_machine'),
                    'change_reason' => $request->input('change_reason'),
                    'notes' => $request->input('notes'),
                    'changed_by' => auth()->user()->name ?? 'System',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plan berhasil diperbarui dan riwayat perubahan dicatat.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function markPlanUrgent(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required',
                'is_urgent' => 'required|boolean'
            ]);

            // Find the plan item
            $planItem = PlanContinuedProduction::where('code_plan', $request->item_id)->first();

            if (!$planItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item plan tidak ditemukan'
                ]);
            }

            // Update urgent status
            $planItem->update([
                'is_urgent' => $request->is_urgent
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status urgent berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
