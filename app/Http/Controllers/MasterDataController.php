<?php

namespace App\Http\Controllers;

use App\Imports\MappingItemsImport;
use App\Models\Machine;
use App\Models\RencanaPlan;
use App\Models\SeriesMaterial;
use App\Models\DatabaseMachine;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Level;
use App\Models\MappingItem;
use App\Models\MasterDataPrepress;
use App\Models\PlanContinuedProduction;
use App\Models\User;
use App\Models\WorkingDays;
use App\Models\HolidayDays;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Database;
// use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;
use Yajra\DataTables\Facades\DataTables;
use PDO;
use App\Models\MasterMachine;

class MasterDataController extends Controller
{

    // MACHINE

    public function indexMachine()
    {
        return view('main.master.master-machine');
    }

    public function machineIndexDataDetail(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {

            $query = Machine::all();

            return DataTables::of($query)
                ->addIndexColumn()
                ->make(true);
        }

        return view('main.master.master-machine');
    }

    public function machineStore(Request $request)
    {
        try {
            // Validate request
            // $request->validate([
            //     'machine_code' => 'required|string|max:50|unique:machines,Code',
            //     'machine_name' => 'required|string|max:255',
            //     'machine_unit' => 'required|string|max:20',
            //     'machine_capacity' => 'required|numeric|min:0',
            //     'machine_department' => 'required|string|max:100',
            //     'machine_description' => 'nullable|string|max:500'
            // ]);

            // dd($request->all());

            // Create new machine
            $machine = Machine::create([
                'Code' => $request->machine_code,
                'Description' => $request->machine_description ?? $request->machine_name,
                'Unit' => $request->machine_unit,
                'CapacityPerHour' => $request->machine_capacity,
                'Department' => $request->machine_department
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Machine berhasil ditambahkan',
                'data' => $machine
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating machine: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function machineIndexDataDetails(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {

            if ($request->has('rplan') && !empty($request->rplan)) {
                $query = DB::connection('mysql3')->table('mastermachine')->whereNot('description', 'like', '%JANGAN DIPAKAI%')->where('Department', $request->rplan)->get();
            } else {
                $query = DB::connection('mysql3')->table('mastermachine')->whereNot('description', 'like', '%JANGAN DIPAKAI%')->get();
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->make(true);
        }

        return view('main.master.master-machine');
    }

    // SERIES MATERIAL
    public function indexSeriesMaterial()
    {
        $rencana = RencanaPlan::all();
        return view('main.master.master-series-material', compact('rencana'));
    }

    public function seriesMaterialIndexDataDetail(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $query = SeriesMaterial::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->series_material . '" data-process="' . $item->proses . '" data-department="' . $item->department . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->series_material . '" data-process="' . $item->proses . '" data-department="' . $item->department . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('main.master.master-series-material');
    }

    public function submitMasterSeriesMaterialData(Request $request)
    {
        $data = $request->all();
        // dd($data);
        SeriesMaterial::updateOrCreate(
            [
                'id' => $data['id_series_material'] ?? null, // Use null if id is not provided
            ],
            [
                'series_material' => $data['name'],
                'proses' => $data['process'],
                'department' => $data['department'],
            ]
        );

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    // DATABASE MACHINES
    public function indexDatabaseMachines()
    {
        $rencana = RencanaPlan::all();
        $query = DB::connection('mysql3')->table('mastermachine')->where('description', '!=', '%JANGAN DIPAKAI%')->get();

        return view('main.master.master-database-machines', compact('rencana', 'query'));
    }

    public function indexMappingItem()
    {
        $rencana = RencanaPlan::all();
        $query = DB::connection('mysql3')->table('mastermachine')->where('description', '!=', '%JANGAN DIPAKAI%')->get();

        return view('main.master.master-mapping-item', compact('rencana', 'query'));
    }

    public function uploadMappingItem(Request $request)
    {
        // dd($request->all());
        if ($request->hasFile('file_upload')) {
            MappingItem::truncate();

            $file = $request->file('file_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);

            try {
                FacadesExcel::import(new MappingItemsImport, public_path('uploads/' . $filename));
                // You can parse the file contents and save to the database as needed
            } catch (\Exception $e) {

                return response()->json(['success' => false, 'message' => 'Error processing file: ' . $e->getMessage()], 500);
            }
            // For example, you can read the file and save data to the database

            return response()->json(['success' => true, 'message' => 'File uploaded successfully', 'filename' => $filename]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    public function databaseMachinesIndexDataDetail(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $query = DatabaseMachine::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->series_material . '" data-process="' . $item->proses . '" data-department="' . $item->department . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->series_material . '" data-process="' . $item->proses . '" data-department="' . $item->department . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('main.master.master-database-machines');
    }

    public function submitMasterDatabaseMachinesData(Request $request)
    {
        $data = $request->all();
        // dd($data);
        DatabaseMachine::updateOrCreate([
            'machine_name' => $data['machine_name'],
            'machine_ip' => $data['machine_ip'],
            'machine_port' => $data['machine_port'],
            'machine_db_name' => $data['machine_db_name'],
            'machine_column_name' => $data['machine_column'],
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil disimpan']);
    }

    public function sendMachineJobOrder(Request $request)
    {
        try {
            $machine = $request->machine;
            $currentDate = $request->current_date;

            // Get database connection details for the machine
            $databaseMachines = DatabaseMachine::where('machine_name', $machine)->first();

            if (!$databaseMachines) {
                return response()->json(['error' => 'Machine database configuration not found'], 400);
            }

            // PLAN INCLUDING DATE PLAN $CURRENTDATE IN PLANNEDPRODUCTION
            $plan = PlanContinuedProduction::where('code_machine', $machine)
                ->where('date_plan', $currentDate)
                ->get();

            foreach ($plan as $item) {
                $items = PlanContinuedProduction::where('code_plan', $item->code_plan)
                    ->where('code_machine', $machine)
                    ->get();

                $series = SeriesMaterial::where('department', $item->department)
                    ->where('proses', $item->process)
                    ->get();

                $series_names = $series->pluck('series_material')->filter()->join(',');

                $jobOrders = [];

                if ($series_names) {
                    $seriesArray = explode(',', $series_names);

                    foreach ($seriesArray as $seriesName) {
                        $materialCode = $item->code_item . '.' . trim($seriesName);

                        $jobOrder = DB::connection('mysql3')
                            ->table('joborder')
                            ->where('materialCode', $materialCode)
                            ->first();

                        if ($jobOrder) {
                            $jobOrders[] = $jobOrder->DocNo;
                        }
                    }
                }

                // Insert job orders into machine's database
                if (!empty($jobOrders)) {
                    foreach ($jobOrders as $jobOrderDocNo) {
                        // Create a new database connection for the machine
                        $machineConnection = [
                            'driver' => 'mysql',
                            'host' => $databaseMachines->machine_ip,
                            'port' => $databaseMachines->machine_port,
                            'database' => $databaseMachines->machine_db_name,
                            'username' => $databaseMachines->username,
                            'password' => $databaseMachines->password,
                            'charset' => 'utf8mb4',
                            'collation' => 'utf8mb4_unicode_ci',
                            'prefix' => '',
                            'strict' => false,
                            'options' => [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                PDO::ATTR_EMULATE_PREPARES => false,
                            ],
                        ];

                        dd($machineConnection);

                        // Set the connection
                        Config::set('database.connections.machine_db', $machineConnection);

                        try {
                            // Clear any existing connection
                            DB::purge('machine_db');

                            // Test the connection first
                            $pdo = DB::connection('machine_db')->getPdo();

                            // dd($pdo);

                            // If we get here, connection was successful
                            DB::connection('machine_db')
                                ->table($databaseMachines->machine_column_name)
                                ->insert([
                                    'projectname' => $item->material_name,
                                    'wo' => $item->wo_docno,
                                    'jo' => implode(',', $jobOrders),
                                    'status' => 'PLAN',
                                ]);
                        } catch (\Exception $e) {
                            return response()->json([
                                'error' => 'Failed to connect to machine database',
                                'details' => [
                                    'message' => $e->getMessage(),
                                    'host' => '127.0.0.1',
                                    'port' => $databaseMachines->machine_port,
                                    'database' => $databaseMachines->machine_db_name,
                                    'username' => 'root',
                                    'table' => $databaseMachines->machine_column_name,
                                    'connection' => $machineConnection // Add this to debug
                                ]
                            ], 500);
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Job Order berhasil dikirim']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'General error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function indexUser()
    {
        $levels = Level::all();
        $divisis = Divisi::all();
        $jabatans = Jabatan::all();

        return view('main.master.master-user', compact('levels', 'divisis', 'jabatans'));
    }

    public function userIndexDataDetail(Request $request)
    {
        if ($request->ajax()) {

            $query = User::with('divisiUser', 'jabatanUser', 'levelUser');

            // Filter berdasarkan divisi jika ada
            if ($request->has('filter_divisi') && $request->filter_divisi != '') {
                $query->where('divisi', $request->filter_divisi);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->name . '" data-email="' . $item->email . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->name . '" data-email="' . $item->email . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('main.master.master-user');
    }

    public function submitMasterUserData(Request $request)
    {
        $data = $request->all();

        // dd($data);

        $dataUser = User::find($data['id_usersystem']);

        if ($dataUser) {
            // dd('ada');
            if ($data['password'] == null) {
                User::updateOrCreate(
                    [
                        'id' => $data['id_usersystem'] ?? null, // Use null if id is not provided
                    ],
                    [
                        'name' => $data['fullname'],
                        'username' => $data['username'],
                        'email' => $data['email'],
                        'password' => $dataUser['password'],
                        'string_password' => $data['password'],
                        'level' => $data['level'],
                        'divisi' => $data['divisi'],
                        'jabatan' => $data['jabatan'],
                        'created_by' => auth()->user()->username,
                    ]
                );
            } else {
                User::updateOrCreate(
                    [
                        'id' => $data['id_usersystem'] ?? null, // Use null if id is not provided
                    ],
                    [
                        'name' => $data['fullname'],
                        'username' => $data['username'],
                        'email' => $data['email'],
                        'password' => bcrypt($data['password']),
                        'string_password' => $data['password'],
                        'level' => $data['level'],
                        'divisi' => $data['divisi'],
                        'jabatan' => $data['jabatan'],
                        'created_by' => auth()->user()->username,
                    ]
                );
            }

        } else {
            // dd('tidak');


            User::updateOrCreate(
                [
                    'id' => $data['id_usersystem'] ?? null, // Use null if id is not provided
                ],
                [
                    'name' => $data['fullname'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'string_password' => $data['password'],
                    'level' => $data['level'],
                    'divisi' => $data['divisi'],
                    'jabatan' => $data['jabatan'],
                    'created_by' => auth()->user()->username,
                ]
            );
        }

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function userIndexDataDetailData($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['data' => $user]);
    }

    public function deleteMasterUserData($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        User::destroy($id);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }


    // Mater Divisi
    public function indexDivisi()
    {
        return view('main.master.master-divisi');
        // return view('main.master.master-divisi');
    }

    public function divisiIndexDataDetail(Request $request)
    {
        if ($request->ajax()) {
            $query = Divisi::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->name . '" data-email="' . $item->email . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->name . '" data-email="' . $item->email . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('main.master.master-divisi');
    }

    public function submitMasterDivisiData(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        // dd($data);

        Divisi::updateOrCreate(
            [
                'id' => $data['id_divisi'] ?? null, // Use null if id is not provided
            ],
            [
                'divisi' => $data['d_divisi'],
                'keterangan' => $data['keterangan'],
                'created_by' => auth()->user()->username,
            ]
        );

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function divisiIndexDataDetailData($id)
    {
        // dd($id);
        $divisi = Divisi::find($id);
        if (!$divisi) {
            return response()->json(['error' => 'Divisi not found'], 404);
        }
        return response()->json(['data' => $divisi]);
    }

    public function deleteMasterDivisiData($id)
    {
        // dd($id);
        $divisi = Divisi::find($id);
        // dd($divisi);
        if (!$divisi) {
            return response()->json(['error' => 'Divisi not found'], 404);
        }

        Divisi::destroy($id);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function levelIndexDataDetail(Request $request)
    {
        if ($request->ajax()) {
            $query = Level::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->name . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->name . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('main.master.master-level');
    }

    public function submitMasterLevelData(Request $request)
    {
        $data = $request->all();
        // dd($data);

        Level::updateOrCreate(
            [
                'id' => $data['id_level'] ?? null, // Use null if id is not provided
            ],
            [
                'level' => $data['level'],
                'keterangan' => $data['keterangan'],
                'created_by' => auth()->user()->username,
            ]
        );

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function levelIndexDataDetailData($id)
    {
        // dd($id);
        $level = Level::find($id);
        if (!$level) {
            return response()->json(['error' => 'Level not found'], 404);
        }
        return response()->json(['data' => $level]);
    }

    public function deleteMasterLevelData($id)
    {
        // dd($id);
        $level = Level::find($id);
        // dd($level);
        if (!$level) {
            return response()->json(['error' => 'Level not found'], 404);
        }

        Level::destroy($id);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function indexLevel()
    {
        return view('main.master.master-level');
    }

    // Jabatan
    public function indexJabatan()
    {
        return view('main.master.master-jabatan');
    }

    public function submitMasterJabatanData(Request $request)
    {
        $data = $request->all();
        // dd($data);

        Jabatan::updateOrCreate(
            [
                'id' => $data['id_jabatan'] ?? null, // Use null if id is not provided
            ],
            [
                'jabatan' => $data['jabatan'],
                'keterangan' => $data['keterangan'],
                'created_by' => auth()->user()->username,
            ]
        );

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function jabatanIndexDataDetail(Request $request)
    {
        if ($request->ajax()) {
            $query = Jabatan::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->name . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->name . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('main.master.master-level');
    }

    public function jabatanIndexDataDetailData($id)
    {
        // dd($id);
        $level = Jabatan::find($id);
        if (!$level) {
            return response()->json(['error' => 'Level not found'], 404);
        }
        return response()->json(['data' => $level]);
    }

    public function deleteMasterJabatanData(Request $request, $id)
    {
        $level = Jabatan::find($id);
        // dd($level);
        if (!$level) {
            return response()->json(['error' => 'Level not found'], 404);
        }

        Jabatan::destroy($id);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    // Master Data Prepress
    public function indexMasterDataPrepress()
    {
        return view('main.master.master-data-prepress');
    }

    public function masterDataPrepressIndexDataDetail(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $query = MasterDataPrepress::all();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-name="' . $item->kode . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->kode . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function submitMasterDataPrepress(Request $request)
    {
        $data = $request->all();
        // dd($data);

        MasterDataPrepress::updateOrCreate(
            [
                'id' => $data['id_data_prepress'] ?? null, // Use null if id is not provided
            ],
            [
                'kode' => $data['kode'],
                'keterangan_job' => $data['keterangan'],
                'waktu_job' => $data['waktu_job'],
                'job' => $data['job'],
                'unit_job' => $data['unit_job'],
                'job_rate' => $data['job_rate'],
                'point_job' => $data['point_job'],
                'created_by' => auth()->user()->username,
            ]
        );

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function masterDataPrepressDetail($id)
    {
        $masterDataPrepress = MasterDataPrepress::find($id);
        if (!$masterDataPrepress) {
            return response()->json(['error' => 'Master Data Prepress not found'], 404);
        }
        return response()->json(['data' => $masterDataPrepress]);
    }

    public function deleteMasterDataPrepress($id)
    {
        $masterDataPrepress = MasterDataPrepress::find($id);
        if (!$masterDataPrepress) {
            return response()->json(['error' => 'Master Data Prepress not found'], 404);
        }

        MasterDataPrepress::destroy($id);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    public function masterDataPrepressIndex()
    {
        return view('main.master.master-data-prepress');
    }

    // Working Days
    public function indexWorkingDays()
    {
        return view('main.master.master-work-day');
    }

    public function workingDaysIndexDataDetail(Request $request)
    {
        if ($request->ajax()) {
            $query = WorkingDays::orderBy('day_of_week')->get();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-day="' . $item->day_of_week . '" data-name="' . $item->day_name . '" data-working="' . $item->is_working_day . '" data-hours="' . $item->working_hours . '" data-half="' . $item->is_half_day . '" data-half-hours="' . $item->half_day_hours . '" data-active="' . $item->is_active . '" data-desc="' . $item->description . '">Edit</button>';
                    return $btn_edit;
                })
                ->addColumn('status_working', function ($item) {
                    return $item->is_working_day ? '<span class="badge badge-success">Hari Kerja</span>' : '<span class="badge badge-danger">Hari Libur</span>';
                })
                ->addColumn('status_half', function ($item) {
                    return $item->is_half_day ? '<span class="badge badge-warning">Setengah Hari</span>' : '<span class="badge badge-info">Full Day</span>';
                })
                ->addColumn('status_active', function ($item) {
                    return $item->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                })
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data mr-2" data-id="' . $item->id . '" data-day="' . $item->day_of_week . '" data-name="' . $item->day_name . '" data-working="' . $item->is_working_day . '" data-hours="' . $item->working_hours . '" data-half="' . $item->is_half_day . '" data-half-hours="' . $item->half_day_hours . '" data-active="' . $item->is_active . '" data-desc="' . $item->description . '">Edit</button>';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-name="' . $item->day_name . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status_working', 'status_half', 'status_active'])
                ->make(true);
        }
    }

    public function submitMasterWorkingDaysData(Request $request)
    {
        $data = $request->all();
        // dd($data);

        WorkingDays::updateOrCreate(
            [
                'id' => $data['id_work_day'] ?? null,
            ],
            [
                'day_of_week' => $data['hari'],
                'day_name' => $data['nama_hari'],
                'is_working_day' => $data['status'] ?? false,
                'working_hours' => $data['jam_kerja'],
                'is_half_day' => $data['setengah_hari'] ?? false,
                'half_day_hours' => $data['jam_setengah_hari'],
                'is_active' => $data['status_active'] ?? true,
                'description' => $data['keterangan'],
            ]
        );

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function workingDaysIndexDataDetailData($id)
    {
        $workingDays = WorkingDays::find($id);
        if (!$workingDays) {
            return response()->json(['error' => 'Working Days not found'], 404);
        }
        return response()->json(['data' => $workingDays]);
    }

    public function deleteMasterWorkingDaysData($id)
    {
        $workingDays = WorkingDays::find($id);
        if (!$workingDays) {
            return response()->json(['error' => 'Working Days not found'], 404);
        }
        WorkingDays::destroy($id);
        return response()->json(['message' => 'Data berhasil dihapus']);
    }

    // Holiday Days
    public function indexHolidayDays()
    {
        return view('main.master.master-holiday-day');
    }

    public function holidayDaysIndexDataDetail(Request $request)
    {
        if ($request->ajax()) {
            $query = HolidayDays::orderBy('date', 'desc')->get();

            return DataTables::of($query)
                ->addColumn('action', function ($item) {
                    $btn_edit = '<button type="button" class="btn btn-sm btn-warning edit-data" data-id="' . $item->id . '" data-date="' . $item->date . '" data-type="' . $item->override_type . '" data-hours="' . $item->working_hours . '" data-desc="' . $item->description . '" data-active="' . $item->is_active . '">Edit</button>&nbsp;&nbsp;';
                    $btn_delete = '<button type="button" class="btn btn-sm btn-danger delete-data" data-id="' . $item->id . '" data-date="' . $item->date . '" data-desc="' . $item->description . '">Delete</button>';
                    return $btn_edit . $btn_delete;
                })
                ->addColumn('override_type_badge', function ($item) {
                    $badges = [
                        'holiday' => '<span class="badge badge-danger">Holiday</span>',
                        'working_day' => '<span class="badge badge-success">Working Day</span>',
                        'half_day' => '<span class="badge badge-warning">Half Day</span>',
                        'custom_hours' => '<span class="badge badge-info">Custom Hours</span>'
                    ];
                    return $badges[$item->override_type] ?? '<span class="badge badge-secondary">Unknown</span>';
                })
                ->addColumn('status_active', function ($item) {
                    return $item->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'override_type_badge', 'status_active'])
                ->make(true);
        }
    }

    public function submitMasterHolidayDaysData(Request $request)
    {
        $data = $request->all();
        // dd($data);

        HolidayDays::updateOrCreate(
            [
                'id' => $data['id_holiday_days'] ?? null,
            ],
            [
                'date' => $data['tanggal'],
                'override_type' => $data['jenis_hari'],
                'working_hours' => $data['jam_kerja'],
                'description' => $data['keterangan'],
                'is_active' => $data['status_active'] ?? true,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]
        );

        return response()->json(['success' => 'Success', 'message' => 'Data berhasil disimpan']);
    }

    public function holidayDaysIndexDataDetailData($id)
    {
        $holidayDays = HolidayDays::find($id);
        if (!$holidayDays) {
            return response()->json(['error' => 'Holiday Days not found'], 404);
        }
        return response()->json(['data' => $holidayDays]);
    }

    public function deleteMasterHolidayDaysData($id)
    {
        $holidayDays = HolidayDays::find($id);
        if (!$holidayDays) {
            return response()->json(['error' => 'Holiday Days not found'], 404);
        }

        HolidayDays::destroy($id);

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
