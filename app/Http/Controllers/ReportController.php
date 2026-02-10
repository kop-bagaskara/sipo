<?php

namespace App\Http\Controllers;

use App\Models\AssignJobPrepress;
use App\Models\HandlingJobPrepress;
use App\Models\JenisPekerjaanPrepress;
use App\Models\JobPrepress;
use App\Models\MasterDataPrepress;
use App\Models\PlanFirstProduction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{

    public function indexReportJobOrderPrepress()
    {
        return view('main.process.prepress.data.data-reportjoborder');
    }

    public function indexReportTransportationCost()
    {
        return view('main.report.data-reporttransportationcost');
    }

    public function getReportDataTransportationCost(Request $request)
    {
        if (request()->ajax()) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Jika tidak ada tanggal yang dipilih, kembalikan data kosong
            if (!$startDate && !$endDate) {
                return datatables()->of([])
                    ->addIndexColumn()
                    ->make(true);
            }

            $goodsissueqty = DB::connection('mysql3')
                ->table('goodsissued as gi')
                ->leftJoin('goodsissueh as gh', 'gi.DocNo', '=', 'gh.DocNo')
                ->select(
                    'gh.DocNo as docno',
                    'gh.VehicleNo as plat_nomor',
                    DB::raw('SUM(gi.qty) as total_qty'),
                    'gi.MaterialCode'
                )
                ->where('gh.status', 'invoiced')
                ->whereRaw('YEAR(gh.createdDate) = YEAR(CURDATE())')
                ->whereRaw('LENGTH(TRIM(gh.VehicleNo)) > 4')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('gh.createdDate', [$startDate, $endDate]);
                })
                ->when($startDate && !$endDate, function ($query) use ($startDate) {
                    return $query->where('gh.createdDate', '>=', $startDate);
                })
                ->when($endDate && !$startDate, function ($query) use ($endDate) {
                    return $query->where('gh.createdDate', '<=', $endDate);
                })
                ->groupBy('gh.DocNo', 'gh.VehicleNo', 'gi.MaterialCode')
                ->get();

            // Ambil data workorder secara terpisah untuk menghindari masalah GROUP BY
            $workorderData = DB::connection('mysql3')
                ->table('goodsissueh as gh')
                ->leftJoin('workorderh as wo', 'gh.SODocNo', '=', 'wo.SODocNo')
                ->select(
                    'gh.DocNo as docno',
                    'wo.zgramature',
                    'wo.zpanjangcetak',
                    'wo.zlebarcetak',
                    'wo.zupcetak',
                    'gh.ShipToCode'
                )
                ->where('gh.status', 'invoiced')
                ->whereRaw('YEAR(gh.createdDate) = YEAR(CURDATE())')
                ->whereRaw('LENGTH(TRIM(gh.VehicleNo)) > 4')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('gh.createdDate', [$startDate, $endDate]);
                })
                ->when($startDate && !$endDate, function ($query) use ($startDate) {
                    return $query->where('gh.createdDate', '>=', $startDate);
                })
                ->when($endDate && !$startDate, function ($query) use ($endDate) {
                    return $query->where('gh.createdDate', '<=', $endDate);
                })
                ->get(); // Ubah dari first() ke get()

            // dd($workorderData);

            // Gabungkan data
            $goodsissueqty = $goodsissueqty->map(function ($item) use ($workorderData) {

                // dd($item);
                // Cari workorder data yang sesuai dengan docno
                $workorder = $workorderData->where('docno', $item->docno)->first();

                // dd($workorder);

                if ($workorder && $workorder->zupcetak > 0 && $workorder->zgramature > 0) {
                    // Validasi nilai sebelum melakukan perhitungan
                    $zupcetak = $workorder->zupcetak > 0 ? $workorder->zupcetak : 1;
                    $zgramature = $workorder->zgramature > 0 ? $workorder->zgramature : 1;
                    $zpanjangcetak = $workorder->zpanjangcetak > 0 ? $workorder->zpanjangcetak : 1;
                    $zlebarcetak = $workorder->zlebarcetak > 0 ? $workorder->zlebarcetak : 1;

                    $qty_kg = ($item->total_qty / $zupcetak) * (($zgramature / 1000) * (($zpanjangcetak + 0.0) / 100) * (($zlebarcetak + 0.0) / 100));
                    $material = DB::connection('mysql3')
                        ->table('mastermaterial as mm')
                        ->select('mm.Name')
                        ->where('mm.Code', $item->MaterialCode)
                        ->first();

                    $mastercustomer = DB::connection('mysql3')
                        ->table('mastercustomer as mc')
                        ->select('mc.Name')
                        ->where('mc.Code', $workorder->ShipToCode)
                        ->first();

                    $item->zgramature = $zgramature;
                    $item->zpanjangcetak = $zpanjangcetak;
                    $item->zlebarcetak = $zlebarcetak;
                    $item->zupcetak = $zupcetak;
                    $item->qty_kg = $qty_kg;
                    $item->qty_ton = $item->qty_kg / 1000;
                    $item->shipto = $mastercustomer->Name;
                    $item->material = $material->Name;
                } else {
                    // Set default values jika tidak ada workorder data atau nilai tidak valid
                    $item->zgramature = 1;
                    $item->zpanjangcetak = 1;
                    $item->zlebarcetak = 1;
                    $item->zupcetak = 1; // Default 1 untuk menghindari division by zero
                    $item->qty_kg = $item->total_qty; // Default ke total_qty
                    $item->qty_ton = $item->total_qty / 1000;
                    $item->shipto = '-';
                    $item->material = '-';
                }
                return $item;
            });

            // dd($goodsissueqty);

            // Cari data transportasi dari purchasecostd dan purchasecosth
            $transport = DB::connection('mysql3')
                ->table('purchasecostd as p')
                ->leftJoin('purchasecosth as pch', 'p.DocNo', '=', 'pch.DocNo')
                ->select(
                    'p.DocNo as pcndoc',
                    'p.Description',
                    'p.Cost',
                    'pch.DocDate'
                )
                // ->whereRaw('p.Description LIKE "%W%"')
                ->whereRaw('p.Description NOT LIKE "MD%"') // Jangan ambil yang diawali MD
                ->whereRaw('p.Description NOT LIKE "MULTIDROP%"') // Jangan ambil yang diawali MD
                ->whereRaw('p.Description NOT LIKE "MULTI DROP%"') // Jangan ambil yang diawali MD
                ->whereRaw('LENGTH(TRIM(p.Description)) > 4') // Pastikan ada karakter
                ->orderBy('pch.DocDate', 'asc')
                ->get();

            // Debug: lihat hasil data transport
            Log::info('Transport data found: ' . $transport->count());
            Log::info('Transport data:', $transport->toArray());

            // Fungsi untuk mengekstrak plat nomor dari deskripsi
            function extractPlateNumber($description)
            {
                // Pattern 1: Plat nomor dalam kurung (contoh: "Ongkir Surabaya - Bekasi 1 Ton (B 9616 FXW)")
                if (preg_match('/\(([A-Z]\s*\d+\s*[A-Z]+)\)/', $description, $matches)) {
                    $plate = trim($matches[1]);
                    return formatPlateNumber($plate);
                }

                // Pattern 2: Plat nomor tanpa kurung (contoh: "Ongkir W 8580 UO Surabaya")
                if (preg_match('/([A-Z]\s*\d+\s*[A-Z]+)/', $description, $matches)) {
                    $plate = trim($matches[1]);
                    return formatPlateNumber($plate);
                }

                // Pattern 3: Plat nomor dengan format lain (contoh: "Transport B-9616-FXW")
                if (preg_match('/([A-Z]-?\d+-?[A-Z]+)/', $description, $matches)) {
                    $plate = str_replace('-', '', trim($matches[1]));
                    return formatPlateNumber($plate);
                }

                // Pattern 4: Plat nomor dengan spasi atau dash (contoh: "B 9616 FXW" atau "B-9616-FXW")
                if (preg_match('/([A-Z][\s\-]*\d+[\s\-]*[A-Z]+)/', $description, $matches)) {
                    $plate = preg_replace('/[\s\-]+/', '', trim($matches[1]));
                    return formatPlateNumber($plate);
                }

                return null; // Jika tidak ditemukan
            }

            // Fungsi untuk memformat plat nomor dengan spasi yang benar
            function formatPlateNumber($plate)
            {
                // Hapus semua spasi dan dash yang ada
                $cleanPlate = preg_replace('/[\s\-]+/', '', $plate);

                // Pattern: huruf + angka + huruf (contoh: W8641US)
                if (preg_match('/^([A-Z])(\d+)([A-Z]+)$/', $cleanPlate, $matches)) {
                    $prefix = $matches[1];      // W
                    $numbers = $matches[2];     // 8641
                    $suffix = $matches[3];      // US

                    // Format: W 8641 US
                    return $prefix . ' ' . $numbers . ' ' . $suffix;
                }

                // Pattern: huruf + angka + huruf + huruf (contoh: W9548US)
                if (preg_match('/^([A-Z])(\d+)([A-Z])([A-Z])$/', $cleanPlate, $matches)) {
                    $prefix = $matches[1];      // W
                    $numbers = $matches[2];     // 9548
                    $suffix1 = $matches[3];     // U
                    $suffix2 = $matches[4];     // S

                    // Format: W 9548 US
                    return $prefix . ' ' . $numbers . ' ' . $suffix1 . $suffix2;
                }

                // Jika tidak match dengan pattern di atas, kembalikan asli
                return $plate;
            }

            // Tambahkan plat nomor yang diekstrak ke data transport
            $transport = $transport->map(function ($item) {
                $item->plat_nomor = extractPlateNumber($item->Description);
                return $item;
            });



            // Debug: lihat hasil dengan plat nomor yang diekstrak
            Log::info('Transport data with plate numbers:');
            foreach ($transport as $item) {
                Log::info("DocNo: {$item->pcndoc}, Description: {$item->Description}, Plate: " . ($item->plat_nomor ?? 'NULL'));
            }

            // dd($transport);

            // Gabungkan data transport dan goods issue menjadi satu
            $combinedData = collect();

            foreach ($transport as $transportItem) {
                $platNomor = $transportItem->plat_nomor;
                $purchaseCost = $transportItem->pcndoc;
                $cost = $transportItem->Cost;

                // Cari goods issue yang sesuai dengan plat nomor
                $goodsIssueItems = $goodsissueqty->where('plat_nomor', $platNomor);

                if ($goodsIssueItems->count() > 0) {
                    foreach ($goodsIssueItems as $goodsItem) {
                        $combinedData->push([
                            'plat_nomor' => $platNomor,
                            'purchase_cost_doc' => $purchaseCost,
                            'goods_issue_doc' => $goodsItem->docno,
                            'total_qty' => $goodsItem->total_qty,
                            'qty_kg' => $goodsItem->qty_kg ?? 0,
                            'qty_ton' => $goodsItem->qty_ton ?? 0,
                            'cost' => $cost,
                            'rupiah_per_kg' => ($goodsItem->qty_kg ?? 0) > 0 ? ($cost / ($goodsItem->qty_kg ?? 1)) : 0,
                            'doc_date' => $transportItem->DocDate,
                            'zgramature' => $goodsItem->zgramature ?? 0,
                            'zpanjangcetak' => $goodsItem->zpanjangcetak ?? 0,
                            'zlebarcetak' => $goodsItem->zlebarcetak ?? 0,
                            'zupcetak' => $goodsItem->zupcetak ?? 1,
                            'shipto' => $goodsItem->shipto ?? '-',
                            'material' => $goodsItem->material ?? '-'
                        ]);
                    }
                }
            }

            // Debug: lihat hasil gabungan data
            // dd($combinedData);

            return datatables()->of($combinedData)
                ->addIndexColumn()
                ->make(true);
        }
    }



    public function testMysql3Connection()
    {
        try {
            // Test koneksi ke mysql3
            $testQuery = DB::connection('mysql3')
                ->table('goodissueh')
                ->select(DB::raw('COUNT(*) as total'))
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Koneksi mysql3 berhasil',
                'total_goods_issue' => $testQuery->total ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error koneksi mysql3: ' . $e->getMessage()
            ]);
        }
    }

    public function getReportData(Request $request)
    {
        if (request()->ajax()) {

            $startDate = $request->input('start_date');
            $endDate   = $request->input('end_date');

            $query = JobPrepress::whereIn('status_job', [
                'APPROVED',
                'FINISH',
                'CLOSED',
                'IN PROGRESS',
                'ASSIGNED',
                'PLAN',
                'OPEN'
            ]);

            if ($startDate && $endDate) {
                $query->whereBetween('tanggal_job_order', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            }
            // dd($query->toSql());

            $jobOrders = $query->orderBy('tanggal_job_order', 'desc');

            // dd($jobOrders);

            return datatables()->of($jobOrders)
                ->addColumn('hari', function ($jobOrders) {
                    if (!$jobOrders->tanggal_job_order) {
                        return '-';
                    }
                    return Carbon::parse($jobOrders->tanggal_job_order)->format('dddd');
                })
                // get total handling_status IN PROGRESS
                ->addColumn('pic', function ($jobOrders) {
                    $pic = AssignJobPrepress::where('id_job_order', $jobOrders->id)->first();
                    return ($pic && $pic->user) ? $pic->user->name : '-';
                })
                ->addColumn('kode', function ($jobOrders) {
                    $master = MasterDataPrepress::where('unit_job', $jobOrders->sub_unit_job)->first();
                    if ($master) {
                        return $master->kode;
                    } else {
                        $job = JenisPekerjaanPrepress::where('nama_jenis', $jobOrders->job_order)->first();
                        if ($job) {
                            $masterdata = MasterDataPrepress::where('job', $job->id)->first();
                            return $masterdata ? $masterdata->kode : '-';
                        }
                        return '-';
                    }
                })
                ->addColumn('job_rate', function ($jobOrders) {
                    $master = MasterDataPrepress::where('unit_job', $jobOrders->sub_unit_job)->first();
                    if ($master) {
                        return $master->job_rate;
                    } else {
                        $job = JenisPekerjaanPrepress::where('nama_jenis', $jobOrders->job_order)->first();
                        if ($job) {
                            $masterdata = MasterDataPrepress::where('job', $job->id)->first();
                            return $masterdata ? $masterdata->job_rate : '-';
                        }
                        return '-';
                    }
                })
                ->addColumn('point', function ($jobOrders) {
                    $master = MasterDataPrepress::where('unit_job', $jobOrders->sub_unit_job)->first();
                    if ($master) {
                        return $master->point_job;
                    } else {
                        $job = JenisPekerjaanPrepress::where('nama_jenis', $jobOrders->job_order)->first();
                        if ($job) {
                            $masterdata = MasterDataPrepress::where('job', $job->id)->first();
                            return $masterdata ? $masterdata->point_job : '-';
                        }
                        return '-';
                    }
                })
                ->addColumn('sub_unit_job', function ($jobOrders) {
                    $master = MasterDataPrepress::where('unit_job', $jobOrders->sub_unit_job)->first();
                    if ($master) {
                        return $master->unit_job;
                    } else {
                        $job = JenisPekerjaanPrepress::where('nama_jenis', $jobOrders->job_order)->first();
                        if ($job) {
                            $masterdata = MasterDataPrepress::where('job', $job->id)->first();
                            return $masterdata ? $masterdata->unit_job : '-';
                        }
                        return '-';
                    }
                })
                // ->addColumn('tanggal_deadline', function ($jobOrders) {
                //     return Carbon::parse($jobOrders->tanggal_deadline)->format('DD-MM-YYYY');
                // })
                ->addColumn('in_progress', function ($jobOrders) {
                    // get handling where status in progress
                    $handling = HandlingJobPrepress::where('id_job_order', $jobOrders->id)->where('status_handling', 'IN PROGRESS')->first();
                    return $handling ? $handling->date_handling : 'No';
                })
                ->addColumn('finish', function ($jobOrders) {
                    // get handling where status finish
                    $handling = HandlingJobPrepress::where('id_job_order', $jobOrders->id)->where('status_handling', 'FINISH')->first();
                    return $handling ? $handling->date_handling : 'No';
                })
                ->addColumn('estimated_time', function ($jobOrders) {
                    // get handling where status estimated time
                    $est_job_realtime = $jobOrders->est_job_realtime ?? 0;
                    // format day hour minute
                    $days = floor($est_job_realtime / 86400);
                    $hours = floor(($est_job_realtime % 86400) / 3600);
                    $minutes = floor(($est_job_realtime % 3600) / 60);
                    return $days . ' Hari, ' . $hours . ' Jam, ' . $minutes . ' Menit';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function exportReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = JobPrepress::whereIn('status_job', ['APPROVED', 'FINISH', 'CLOSED', 'IN PROGRESS', 'PLAN', 'OPEN', 'ASSIGNED']);

        if ($startDate && $endDate) {
            // Tambahkan waktu akhir (23:59:59) ke endDate agar include sepanjang hari
            $endDateWithTime = $endDate . ' 23:59:59';
            $query->whereBetween('tanggal_job_order', [$startDate, $endDateWithTime]);
        }

        $jobOrders = $query->with(['handlingJobPrepress', 'assignJobPrepress'])
            ->orderBy('tanggal_job_order', 'desc')
            ->get();

        $filename = 'Report_Job_Prepress_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($jobOrders) implements FromCollection, WithHeadings, WithMapping {
            private $jobOrders;

            public function __construct($jobOrders)
            {
                $this->jobOrders = $jobOrders;
            }

            public function collection()
            {
                return $this->jobOrders;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Hari',
                    'PIC',
                    'Kode',
                    'Job Rate',
                    'Point',
                    'Sub Unit Job',
                    'Tanggal Job Order',
                    'Deadline',
                    'Customer',
                    'Product',
                    'Kode Design',
                    'Dimension',
                    'Material',
                    'Job Order',
                    'Qty Order',
                    'Status Job',
                    'Prioritas',
                    'In Progress At',
                    'Finish At',
                    'Estimasi (Menit)',
                    'Received At',
                    'Received By',
                    'Created By',
                    'Created At'
                ];
            }

            public function map($job): array
            {
                static $no = 1;

                // Get PIC data
                $pic = $job->assignJobPrepress ? $job->assignJobPrepress->user->name : '-';

                // Get Master Data Prepress with fallback logic
                $master = \App\Models\MasterDataPrepress::where('unit_job', $job->sub_unit_job)->first();

                if ($master) {
                    $kode = $master->kode;
                    $jobRate = $master->job_rate;
                    $point = $master->point_job;
                    $subUnitJob = $master->unit_job;
                } else {
                    $jenisJob = \App\Models\JenisPekerjaanPrepress::where('nama_jenis', $job->job_order)->first();
                    if ($jenisJob) {
                        $masterData = \App\Models\MasterDataPrepress::where('job', $jenisJob->id)->first();
                        if ($masterData) {
                            $kode = $masterData->kode;
                            $jobRate = $masterData->job_rate;
                            $point = $masterData->point_job;
                            $subUnitJob = $masterData->unit_job;
                        } else {
                            $kode = '-';
                            $jobRate = '-';
                            $point = '-';
                            $subUnitJob = '-';
                        }
                    } else {
                        $kode = '-';
                        $jobRate = '-';
                        $point = '-';
                        $subUnitJob = '-';
                    }
                }

                // Get handling data
                $inProgressHandling = $job->handlingJobPrepress->where('status_handling', 'IN PROGRESS')->first();
                $finishHandling = $job->handlingJobPrepress->where('status_handling', 'FINISH')->first();

                $inProgressAt = $inProgressHandling ? $inProgressHandling->date_handling : '-';
                $finishAt = $finishHandling ? $finishHandling->date_handling : '-';

                // Calculate estimated time
                $estJobRealtime = $job->est_job_realtime;
                $days = floor($estJobRealtime / 86400);
                $hours = floor(($estJobRealtime % 86400) / 3600);
                $minutes = floor(($estJobRealtime % 3600) / 60);
                $estimatedTime = $days . ' Hari, ' . $hours . ' Jam, ' . $minutes . ' Menit';

                // Format hari dalam bahasa Indonesia
                $hariMapping = [
                    'Monday' => 'Senin',
                    'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu',
                    'Thursday' => 'Kamis',
                    'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                    'Sunday' => 'Minggu'
                ];

                $dayOfWeek = Carbon::parse($job->tanggal_job_order)->format('l');
                $hariIndonesia = $hariMapping[$dayOfWeek] ?? $dayOfWeek;

                return [
                    $no++,
                    $hariIndonesia,
                    $pic,
                    $kode,
                    $jobRate,
                    $point,
                    $subUnitJob,
                    Carbon::parse($job->tanggal_job_order)->format('d-m-Y'),
                    Carbon::parse($job->tanggal_deadline)->format('d-m-Y'),
                    $job->customer ?: '-',
                    $job->product ?: '-',
                    $job->kode_design ?: '-',
                    $job->dimension ?: '-',
                    $job->material ?: '-',
                    $job->job_order ?: '-',
                    $job->qty_order_estimation ?: '-',
                    $job->status_job ?: '-',
                    $job->prioritas_job ?: '-',
                    $inProgressAt,
                    $finishAt,
                    $estimatedTime,
                    $job->received_at ? Carbon::parse($job->received_at)->format('d-m-Y H:i') : '-',
                    $job->received_by ?: '-',
                    $job->created_by ?: '-',
                    Carbon::parse($job->created_at)->format('d-m-Y H:i')
                ];
            }
        }, $filename);
    }

    public function exportTransportationCost(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Jika tidak ada tanggal yang dipilih, kembalikan error
        if (!$startDate && !$endDate) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih tanggal mulai atau tanggal akhir terlebih dahulu!'
            ]);
        }

        $goodsissueqty = DB::connection('mysql3')
            ->table('goodsissued as gi')
            ->leftJoin('goodsissueh as gh', 'gi.DocNo', '=', 'gh.DocNo')
            ->select(
                'gh.DocNo as docno',
                'gh.VehicleNo as plat_nomor',
                DB::raw('SUM(gi.qty) as total_qty')
            )
            ->where('gh.status', 'invoiced')
            ->whereRaw('YEAR(gh.createdDate) = YEAR(CURDATE())')
            ->whereRaw('LENGTH(TRIM(gh.VehicleNo)) > 4')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('gh.createdDate', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                return $query->where('gh.createdDate', '>=', $startDate);
            })
            ->when($endDate && !$startDate, function ($query) use ($endDate) {
                return $query->where('gh.createdDate', '<=', $endDate);
            })
            ->groupBy('gh.DocNo', 'gh.VehicleNo')
            ->get();

        // Ambil data workorder secara terpisah untuk menghindari masalah GROUP BY
        $workorderData = DB::connection('mysql3')
            ->table('goodsissueh as gh')
            ->leftJoin('workorderh as wo', 'gh.SODocNo', '=', 'wo.SODocNo')
            ->select(
                'gh.DocNo as docno',
                'wo.zgramature',
                'wo.zpanjangcetak',
                'wo.zlebarcetak',
                'wo.zupcetak',
                'gh.ShipToCode'
            )
            ->where('gh.status', 'invoiced')
            ->whereRaw('YEAR(gh.createdDate) = YEAR(CURDATE())')
            ->whereRaw('LENGTH(TRIM(gh.VehicleNo)) > 4')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('gh.createdDate', [$startDate, $endDate]);
            })
            ->when($startDate && !$endDate, function ($query) use ($startDate) {
                return $query->where('gh.createdDate', '>=', $startDate);
            })
            ->when($endDate && !$startDate, function ($query) use ($endDate) {
                return $query->where('gh.createdDate', '<=', $endDate);
            })
            ->get();

        // Gabungkan data
        $goodsissueqty = $goodsissueqty->map(function ($item) use ($workorderData) {
            // Cari workorder data yang sesuai dengan docno
            $workorder = $workorderData->where('docno', $item->docno)->first();

            if ($workorder && $workorder->zupcetak > 0 && $workorder->zgramature > 0) {
                // Validasi nilai sebelum melakukan perhitungan
                $zupcetak = $workorder->zupcetak > 0 ? $workorder->zupcetak : 1;
                $zgramature = $workorder->zgramature > 0 ? $workorder->zgramature : 1;
                $zpanjangcetak = $workorder->zpanjangcetak > 0 ? $workorder->zpanjangcetak : 1;
                $zlebarcetak = $workorder->zlebarcetak > 0 ? $workorder->zlebarcetak : 1;

                $qty_kg = ($item->total_qty / $zupcetak) * (($zgramature / 1000) * (($zpanjangcetak + 0.0) / 100) * (($zlebarcetak + 0.0) / 100));

                $material = DB::connection('mysql3')
                    ->table('mastermaterial as mm')
                    ->select('mm.Material')
                    ->where('mm.Material', $item->Material)
                    ->first();

                $mastercustomer = DB::connection('mysql3')
                    ->table('mastercustomer as mc')
                    ->select('mc.ShipToCode', 'mc.ShipToName')
                    ->where('mc.ShipToCode', $item->ShipToCode)
                    ->first();

                $item->zgramature = $zgramature;
                $item->zpanjangcetak = $zpanjangcetak;
                $item->zlebarcetak = $zlebarcetak;
                $item->zupcetak = $zupcetak;
                $item->qty_kg = $qty_kg;
                $item->qty_ton = $item->qty_kg / 1000;
            } else {
                // Set default values jika tidak ada workorder data atau nilai tidak valid
                $item->zgramature = 1;
                $item->zpanjangcetak = 1;
                $item->zlebarcetak = 1;
                $item->zupcetak = 1; // Default 1 untuk menghindari division by zero
                $item->qty_kg = $item->total_qty; // Default ke total_qty
                $item->qty_ton = $item->total_qty / 1000;
            }
            return $item;
        });

        // Cari data transportasi dari purchasecostd dan purchasecosth
        $transport = DB::connection('mysql3')
            ->table('purchasecostd as p')
            ->leftJoin('purchasecosth as pch', 'p.DocNo', '=', 'pch.DocNo')
            ->select(
                'p.DocNo as pcndoc',
                'p.Description',
                'p.Cost',
                'pch.DocDate'
            )
            // Filter yang lebih fleksibel untuk mencari data transportasi
            ->where(function ($query) {
                $query->whereRaw('p.Description LIKE "%W%"') // Yang ada huruf W (plat nomor)
                    ->orWhereRaw('p.Description LIKE "%ongkir%"') // Yang ada kata "ongkir"
                    ->orWhereRaw('p.Description LIKE "%transport%"') // Yang ada kata "transport"
                    ->orWhereRaw('p.Description LIKE "%kirim%"'); // Yang ada kata "kirim"
            })
            ->whereRaw('LENGTH(TRIM(p.Description)) > 4') // Pastikan ada karakter
            ->whereRaw('p.Description NOT LIKE "MD%"') // Jangan ambil yang diawali MD
            ->orderBy('pch.DocDate', 'asc')
            ->get();

        // Debug: lihat hasil data transport
        // dd('Transport data found: ' . $transport->count());
        // dd('Transport data:', $transport->toArray());

        // Fungsi untuk mengekstrak plat nomor dari deskripsi
        function extractPlateNumber($description)
        {
            // Pattern 1: Plat nomor dalam kurung (contoh: "Ongkir Surabaya - Bekasi 1 Ton (B 9616 FXW)")
            if (preg_match('/\(([A-Z]\s*\d+\s*[A-Z]+)\)/', $description, $matches)) {
                $plate = trim($matches[1]);
                return formatPlateNumber($plate);
            }

            // Pattern 2: Plat nomor tanpa kurung (contoh: "Ongkir W 8580 UO Surabaya")
            if (preg_match('/([A-Z]\s*\d+\s*[A-Z]+)/', $description, $matches)) {
                $plate = trim($matches[1]);
                return formatPlateNumber($plate);
            }

            // Pattern 3: Plat nomor dengan format lain (contoh: "Transport B-9616-FXW")
            if (preg_match('/([A-Z]-?\d+-?[A-Z]+)/', $description, $matches)) {
                $plate = str_replace('-', '', trim($matches[1]));
                return formatPlateNumber($plate);
            }

            // Pattern 4: Plat nomor dengan spasi atau dash (contoh: "B 9616 FXW" atau "B-9616-FXW")
            if (preg_match('/([A-Z][\s\-]*\d+[\s\-]*[A-Z]+)/', $description, $matches)) {
                $plate = preg_replace('/[\s\-]+/', '', trim($matches[1]));
                return formatPlateNumber($plate);
            }

            return null; // Jika tidak ditemukan
        }

        // Fungsi untuk memformat plat nomor dengan spasi yang benar
        function formatPlateNumber($plate)
        {
            // Hapus semua spasi dan dash yang ada
            $cleanPlate = preg_replace('/[\s\-]+/', '', $plate);

            // Pattern: huruf + angka + huruf (contoh: W8641US)
            if (preg_match('/^([A-Z])(\d+)([A-Z]+)$/', $cleanPlate, $matches)) {
                $prefix = $matches[1];      // W
                $numbers = $matches[2];     // 8641
                $suffix = $matches[3];      // US

                // Format: W 8641 US
                return $prefix . ' ' . $numbers . ' ' . $suffix;
            }

            // Pattern: huruf + angka + huruf + huruf (contoh: W9548US)
            if (preg_match('/^([A-Z])(\d+)([A-Z])([A-Z])$/', $cleanPlate, $matches)) {
                $prefix = $matches[1];      // W
                $numbers = $matches[2];     // 9548
                $suffix1 = $matches[3];     // U
                $suffix2 = $matches[4];     // S

                // Format: W 9548 US
                return $prefix . ' ' . $numbers . ' ' . $suffix1 . $suffix2;
            }

            // Jika tidak match dengan pattern di atas, kembalikan asli
            return $plate;
        }

        // Tambahkan plat nomor yang diekstrak ke data transport
        $transport = $transport->map(function ($item) {
            $item->plat_nomor = extractPlateNumber($item->Description);
            return $item;
        });

        // Debug: lihat hasil dengan plat nomor yang diekstrak
        // dd('Transport data with plate numbers:');
        // foreach ($transport as $item) {
        //     dd("DocNo: {$item->pcndoc}, Description: {$item->Description}, Plate: " . ($item->plat_nomor ?? 'NULL'));
        // }

        // Debug: lihat goods issue data
        // dd('Goods issue data found: ' . $goodsissueqty->count());
        // dd('Unique plate numbers in goods issue: ' . $goodsissueqty->pluck('plat_nomor')->unique()->count());
        // dd('Plate numbers in goods issue:', $goodsissueqty->pluck('plat_nomor')->unique()->toArray());

        // Debug: lihat data yang akan digabung
        // dd('Transport items with plate numbers:', $transport->whereNotNull('plat_nomor')->toArray());

        // Debug: lihat data yang berhasil diekstrak plat nomornya
        $transportWithPlates = $transport->whereNotNull('plat_nomor');
        // dd('Transport items with valid plate numbers: ' . $transportWithPlates->count());
        // dd('Valid plate numbers found:', $transportWithPlates->pluck('plat_nomor')->toArray());

        // Gabungkan data transport dan goods issue menjadi satu
        $combinedData = collect();

        foreach ($transport as $transportItem) {
            $platNomor = $transportItem->plat_nomor;
            $purchaseCost = $transportItem->pcndoc;
            $cost = $transportItem->Cost;

            // Cari goods issue yang sesuai dengan plat nomor
            $goodsIssueItems = $goodsissueqty->where('plat_nomor', $platNomor);

            if ($goodsIssueItems->count() > 0) {
                foreach ($goodsIssueItems as $goodsItem) {
                    $combinedData->push([
                        'plat_nomor' => $platNomor,
                        'purchase_cost_doc' => $purchaseCost,
                        'goods_issue_doc' => $goodsItem->docno,
                        'total_qty' => $goodsItem->total_qty,
                        'qty_kg' => $goodsItem->qty_kg ?? 0,
                        'qty_ton' => $goodsItem->qty_ton ?? 0,
                        'cost' => $cost,
                        'rupiah_per_kg' => ($goodsItem->qty_kg ?? 0) > 0 ? ($cost / ($goodsItem->qty_kg ?? 1)) : 0,
                        'doc_date' => $transportItem->DocDate,
                        'zgramature' => $goodsItem->zgramature ?? 0,
                        'zpanjangcetak' => $goodsItem->zpanjangcetak ?? 0,
                        'zlebarcetak' => $goodsItem->zlebarcetak ?? 0,
                        'zupcetak' => $goodsItem->zupcetak ?? 1,
                        'shipto' => $goodsItem->shipto ?? '-',
                        'material' => $goodsItem->material ?? '-'
                    ]);
                }
            }
        }

        // Group data by plat nomor untuk export
        $exportData = collect();
        $groupedData = $combinedData->groupBy('plat_nomor');

        foreach ($groupedData as $platNomor => $items) {
            $firstItem = $items->first();
            $totalQtyKg = $items->sum('qty_kg');
            $totalCost = $firstItem['cost'];
            $rupiahPerKg = $totalQtyKg > 0 ? ($totalCost / $totalQtyKg) : 0;

            $exportData->push([
                'plat_nomor' => $platNomor,
                'purchase_cost_doc' => $firstItem['purchase_cost_doc'],
                'goods_issue_docs' => $items->pluck('goods_issue_doc')->implode(', '),
                'total_qty_kg' => $totalQtyKg,
                'total_cost' => $totalCost,
                'rupiah_per_kg' => $rupiahPerKg,
                'doc_date' => $firstItem['doc_date']
            ]);
        }

        $filename = 'Report_Transportation_Cost_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($exportData) implements FromCollection, WithHeadings, WithMapping {
            private $exportData;

            public function __construct($exportData)
            {
                $this->exportData = $exportData;
            }

            public function collection()
            {
                return $this->exportData;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Plat Nomor',
                    'PCN DocNo',
                    'Goods Issue DocNo',
                    'Total Qty (KG)',
                    'Total Cost (Rp)',
                    'Rupiah per KG',
                    'Tanggal'
                ];
            }

            public function map($item): array
            {
                static $no = 1;
                return [
                    $no++,
                    $item['plat_nomor'] ?? '-',
                    $item['purchase_cost_doc'] ?? '-',
                    $item['goods_issue_docs'] ?? '-',
                    number_format($item['total_qty_kg'] ?? 0, 2) . ' KG',
                    'Rp ' . number_format($item['total_cost'] ?? 0, 0, ',', '.'),
                    'Rp ' . number_format($item['rupiah_per_kg'] ?? 0, 2, ',', '.'),
                    $item['doc_date'] ? Carbon::parse($item['doc_date'])->format('d-m-Y') : '-'
                ];
            }
        }, $filename);
    }

    public function indexWorkOrderPercentage()
    {
        return view('main.report.data-reportworkorderpercentage');
    }

    public function getReportDataWorkOrderPercentage()
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        // Ambil data Work Order dengan MaterialCode
        $workorder = DB::connection('mysql3')
            ->table('workorderh as wo')
            ->select(
                'wo.DocNo',
                'wo.MaterialCode',
                'wo.zgramature',
                'wo.zpanjangcetak',
                'wo.zlebarcetak',
                'wo.zupcetak',
                'wo.Unit'
            )
            // ->where('Docno', 'WOT-240910-0001')
            ->where('DocNo', 'like', '%WOT%')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('wo.createdDate', [$startDate, $endDate]);
            })
            ->get();

        // dd($workorder);

        // Cari Job Order yang finish (status finish) berdasarkan MaterialCode dari Work Order
        $jobOrderAnalysis = collect();

        foreach ($workorder as $wo) {
            $materialCode = $wo->MaterialCode;
            $woDocNo = $wo->DocNo;
            // dd($woDocNo);

            // dd($materialCode);

            if (!$materialCode) continue;

            // Cari Job Order WIP.PTG yang finish
            $wipPtgJobOrders = DB::connection('mysql3')
                ->table('joborder as jo')
                ->select(
                    'jo.MaterialCode',
                    DB::raw('SUM(jo.QtyTarget) as total_qty_wip_ptg'),
                    'jo.WODocNo',
                    'jo.IODocNo',
                    'jo.DocNo'
                )
                ->where(function ($query) use ($materialCode) {
                    $query->where('jo.MaterialCode', 'LIKE', '%' . $materialCode . '%WIP.PTG%')
                        ->orWhere('jo.MaterialCode', 'LIKE', '%' . $materialCode . '%WIP.TUM%');
                })
                ->where(function ($query) use ($woDocNo) {
                    $query->where('jo.WODocNo', $woDocNo)
                        ->orWhere('jo.IODocNo', $woDocNo);
                })
                ->where('jo.Status', 'finish') // Hanya yang finish
                // ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                //     return $query->whereBetween('jo.CreatedDate', [$startDate, $endDate]);
                // })
                ->groupBy('jo.DocNo', 'jo.MaterialCode', 'jo.WODocNo', 'jo.IODocNo', 'jo.DocNo')
                ->get();

            // dd($wipPtgJobOrders);

            // Cari Job Order Barang Jadi/Finishing yang finish
            $finishedJobOrders = DB::connection('mysql3')
                ->table('joborder as jo')
                ->select(
                    'jo.MaterialCode',
                    DB::raw('SUM(jo.QtyOutput) as total_qty_finished'),
                    'jo.WODocNo',
                    'jo.IODocNo',
                    'jo.DocNo'
                )
                ->where(function ($query) use ($materialCode) {
                    $query->where('jo.MaterialCode', '=', $materialCode);
                })
                ->where(function ($query) use ($woDocNo) {
                    $query->where('jo.WODocNo', $woDocNo)
                        ->orWhere('jo.IODocNo', $woDocNo);
                })
                ->where('jo.Status', 'FINISH')
                ->groupBy('jo.MaterialCode', 'jo.WODocNo', 'jo.IODocNo', 'jo.DocNo')
                ->get();


            // dd($finishedJobOrders);

            // Hitung total quantity
            $totalQtyWipPtg = $wipPtgJobOrders->sum('total_qty_wip_ptg');
            $totalQtyFinished = $finishedJobOrders->sum('total_qty_finished');

            // Hitung WIP.PTG yang sudah dibagi UP (zupcetak)
            $totalQtyWipPtgAdjusted = 0;
            if ($totalQtyWipPtg > 0 && $wo->zupcetak > 0) {
                $totalQtyWipPtgAdjusted = $totalQtyWipPtg * $wo->zupcetak;
            }

            // dd($wipPtgJobOrders);

            $jos = $finishedJobOrders[0]->DocNo ?? '-';

            // dimulai JOS
            if (strpos($jos, 'JOS') !== false) {
                $jos = 'JOS';
            } else {
                $jos = '-';
            }

            // dd($jos);

            // Hitung presentase
            $percentage = 0;
            if ($totalQtyWipPtgAdjusted > 0 && $totalQtyFinished > 0) {
                $percentage = ($totalQtyFinished / $totalQtyWipPtgAdjusted) * 100;
            }

            $materialName = DB::connection('mysql3')
                ->table('mastermaterial as mm')
                ->select('mm.Name')
                ->where('mm.Code', $materialCode)
                ->first();

            // dd($materialName);

            // dd($wipPtgJobOrders);

            // $materialCodeJobOrder = $wipPtgJobOrders->first()->MaterialCode;

            $jobOrderAnalysis->push([
                'work_order_doc' => $wo->DocNo,
                'material_code' => $materialCode,
                'material_name' => $materialName->Name ?? '-',
                'total_qty_wip_ptg' => $totalQtyWipPtg,
                'total_qty_wip_ptg_adjusted' => round($totalQtyWipPtgAdjusted, 2),
                'total_qty_finished' => $totalQtyFinished,
                'unit' => $wo->Unit,
                'percentage' => round($percentage, 2),
                'zgramature' => $wo->zgramature,
                'zpanjangcetak' => $wo->zpanjangcetak,
                'zlebarcetak' => $wo->zlebarcetak,
                'zupcetak' => $wo->zupcetak,
                'jos' => $jos ?? '-'
            ]);
        }

        // Debug: lihat hasil analisis
        // dd($jobOrderAnalysis);

        return datatables()->of($jobOrderAnalysis)
            ->addIndexColumn()
            ->make(true);
    }

    public function exportWorkOrderPercentage(Request $request)
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        // Ambil data Work Order dengan MaterialCode
        $workorder = DB::connection('mysql3')
            ->table('workorderh as wo')
            ->select(
                'wo.DocNo',
                'wo.MaterialCode',
                'wo.zgramature',
                'wo.zpanjangcetak',
                'wo.zlebarcetak',
                'wo.zupcetak',
                'wo.Unit'
            )
            ->where('DocNo', 'like', '%WOT%')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('wo.createdDate', [$startDate, $endDate]);
            })
            ->get();

        // Cari Job Order yang finish (status finish) berdasarkan MaterialCode dari Work Order
        $jobOrderAnalysis = collect();

        foreach ($workorder as $wo) {
            $materialCode = $wo->MaterialCode;
            $woDocNo = $wo->DocNo;

            if (!$materialCode) continue;

            // Cari Job Order WIP.PTG yang finish
            $wipPtgJobOrders = DB::connection('mysql3')
                ->table('joborder as jo')
                ->select(
                    'jo.MaterialCode',
                    DB::raw('SUM(jo.QtyTarget) as total_qty_wip_ptg'),
                    'jo.WODocNo',
                    'jo.IODocNo',
                    'jo.DocNo'
                )
                ->where(function ($query) use ($materialCode) {
                    $query->where('jo.MaterialCode', 'LIKE', '%' . $materialCode . '%WIP.PTG%')
                        ->orWhere('jo.MaterialCode', 'LIKE', '%' . $materialCode . '%WIP.TUM%');
                })
                ->where(function ($query) use ($woDocNo) {
                    $query->where('jo.WODocNo', $woDocNo)
                        ->orWhere('jo.IODocNo', $woDocNo);
                })
                ->where('jo.Status', 'finish') // Hanya yang finish
                // ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                //     return $query->whereBetween('jo.CreatedDate', [$startDate, $endDate]);
                // })
                ->groupBy('jo.DocNo', 'jo.MaterialCode', 'jo.WODocNo', 'jo.IODocNo', 'jo.DocNo')
                ->get();

            // Cari Job Order Barang Jadi/Finishing yang finish
            $finishedJobOrders = DB::connection('mysql3')
                ->table('joborder as jo')
                ->select(
                    'jo.MaterialCode',
                    DB::raw('SUM(jo.QtyOutput) as total_qty_finished'),
                    'jo.WODocNo',
                    'jo.IODocNo',
                    'jo.DocNo'
                )
                ->where(function ($query) use ($materialCode) {
                    $query->where('jo.MaterialCode', '=', $materialCode);
                })
                ->where(function ($query) use ($woDocNo) {
                    $query->where('jo.WODocNo', $woDocNo)
                        ->orWhere('jo.IODocNo', $woDocNo);
                })
                ->where('jo.Status', 'FINISH')
                ->groupBy('jo.MaterialCode', 'jo.WODocNo', 'jo.IODocNo', 'jo.DocNo')
                ->get();

            // Hitung total quantity
            $totalQtyWipPtg = $wipPtgJobOrders->sum('total_qty_wip_ptg');
            $totalQtyFinished = $finishedJobOrders->sum('total_qty_finished');

            $jos = $finishedJobOrders[0]->DocNo ?? '-';

            // dimulai JOS
            if (strpos($jos, 'JOS') !== false) {
                $jos = 'JOS';
            } else {
                $jos = '-';
            }

            // Hitung WIP.PTG yang sudah dibagi UP (zupcetak)
            $totalQtyWipPtgAdjusted = 0;
            if ($totalQtyWipPtg > 0 && $wo->zupcetak > 0) {
                $totalQtyWipPtgAdjusted = $totalQtyWipPtg * $wo->zupcetak;
            }

            // Hitung presentase
            $percentage = 0;
            if ($totalQtyWipPtgAdjusted > 0 && $totalQtyFinished > 0) {
                $percentage = ($totalQtyFinished / $totalQtyWipPtgAdjusted) * 100;
            }

            $materialName = DB::connection('mysql3')
                ->table('mastermaterial as mm')
                ->select('mm.Name')
                ->where('mm.Code', $materialCode)
                ->first();

            $jobOrderAnalysis->push([
                'work_order_doc' => $wo->DocNo,
                'material_code' => $materialCode,
                'material_name' => $materialName->Name ?? '-',
                'total_qty_wip_ptg' => $totalQtyWipPtg,
                'total_qty_wip_ptg_adjusted' => round($totalQtyWipPtgAdjusted, 2),
                'total_qty_finished' => $totalQtyFinished,
                'unit' => $wo->Unit,
                'percentage' => round($percentage, 2),
                'zgramature' => $wo->zgramature,
                'zpanjangcetak' => $wo->zpanjangcetak,
                'zlebarcetak' => $wo->zlebarcetak,
                'zupcetak' => $wo->zupcetak,
                'jos' => $jos ?? '-'
            ]);
        }

        $filename = 'Report_WorkOrder_Percentage_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($jobOrderAnalysis) implements FromCollection, WithHeadings, WithMapping {
            private $jobOrderAnalysis;

            public function __construct($jobOrderAnalysis)
            {
                $this->jobOrderAnalysis = $jobOrderAnalysis;
            }

            public function collection()
            {
                return $this->jobOrderAnalysis;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Work Order Doc',
                    'Material Code',
                    'Material Name',
                    'JOS',
                    'Total Qty WIP.PTG',
                    'Total Qty WIP.PTG Adjusted',
                    'Total Qty Finished',
                    'Unit',
                    'Percentage (%)',
                    'UP (zupcetak)',
                    'zgramature',
                    'zpanjangcetak',
                    'zlebarcetak'
                ];
            }

            public function map($item): array
            {
                static $no = 1;
                return [
                    $no++,
                    $item['work_order_doc'] ?? '-',
                    $item['material_code'] ?? '-',
                    $item['material_name'] ?? '-',
                    $item['jos'] ?? '-',
                    $item['total_qty_wip_ptg'] ?? 0,
                    $item['total_qty_wip_ptg_adjusted'] ?? 0,
                    $item['total_qty_finished'] ?? 0,
                    $item['unit'] ?? '-',
                    $item['percentage'] ?? 0,
                    $item['zupcetak'] ?? 0,
                    $item['zgramature'] ?? 0,
                    $item['zpanjangcetak'] ?? 0,
                    $item['zlebarcetak'] ?? 0
                ];
            }
        }, $filename);
    }

    public function indexReportPlanProduction()
    {
        return view('main.report.data-reportplanproduction');
    }

    public function getReportDataPlanProduction(Request $request)
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $data = PlanFirstProduction::whereBetween('created_at', [$startDate, $endDate])->get();

        return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);
    }

    public function exportReportPlanProduction(Request $request)
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        $data = PlanFirstProduction::whereBetween('created_at', [$startDate, $endDate])->get();

        $filename = 'Report_Plan_Production_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($data) implements FromCollection, WithHeadings, WithMapping {
            private $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Material Code',
                    'Material Name',
                    'Machine',
                    'WO',
                    'SO',
                    'Quantity',
                    'Start Date',
                    'End Date',
                    'Status'
                ];
            }

            public function map($item): array
            {
                static $no = 1;
                return [
                    $no++,
                    $item['code_item'] ?? '-',
                    $item['material_name'] ?? '-',
                    $item['code_machine'] ?? '-',
                    $item['wo_docno'] ?? '-',
                    $item['so_docno'] ?? '-',
                    $item['quantity'] ?? 0,
                    $item['start_jam'] ?? '-',
                    $item['end_jam'] ?? '-',
                    $item['flag_status'] ?? '-'
                ];
            }
        }, $filename);
    }

    public function indexReportWorkOrderGoodIssue()
    {
        return view('main.report.data-reportworkordergoodissue');
    }

    public function getReportDataWorkOrderGoodIssue(Request $request)
    {
        $startDate = request('start_date');
        $endDate = request('end_date');

        // Validasi input tanggal
        if (!$startDate && !$endDate) {
            return response()->json([
                'data' => [],
                'message' => 'Silakan pilih tanggal mulai atau tanggal akhir'
            ]);
        }

        try {
            $data = DB::connection('mysql3')->table('workorderh')
                ->leftJoin('mastermaterial as mm', 'workorderh.MaterialCode', '=', 'mm.Code')
                ->join('goodsissueh as gh', 'workorderh.SODocNo', '=', 'gh.SODocNo') // INNER JOIN
                ->join('goodsissued as gi', 'gh.DocNo', '=', 'gi.DocNo')             // INNER JOIN
                ->where(function ($query) {
                    $query->where('workorderh.DocNo', 'like', '%WOT%')
                        ->orWhere('workorderh.DocNo', 'like', '%WOP%');
                })
                ->whereBetween('workorderh.createdDate', [$startDate, $endDate])
                ->select(
                    'workorderh.DocNo',
                    'workorderh.SODocNo',
                    'workorderh.MaterialCode',
                    'workorderh.Qty',
                    'mm.Name as material_name',
                    'gh.DocNo as GoodsIssueDocNo',
                    DB::raw('SUM(gi.Qty) as GoodsIssueQty'),
                    'gi.Unit'
                )
                ->groupBy(
                    'workorderh.DocNo',
                    'workorderh.SODocNo',
                    'workorderh.MaterialCode',
                    'workorderh.Qty',
                    'mm.Name',
                    'gh.DocNo',
                    'gi.Unit'
                )
                ->get();

            return response()->json([
                'data' => $data,
                'message' => 'Data berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getReportDataWorkOrderGoodIssue: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
}
