<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaster;
use App\Models\TrainingParticipant;
use App\Models\TrainingDepartment;
use App\Models\User;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    /**
     * Display a listing of trainings
     */
    public function index(Request $request)
    {
        $query = TrainingMaster::with(['creator', 'participants']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by training type
        if ($request->has('training_type') && $request->training_type !== '') {
            $query->where('training_type', $request->training_type);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id !== '') {
            $query->forDepartment($request->department_id);
        }

        // Search by name or code
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('training_name', 'like', "%{$search}%")
                  ->orWhere('training_code', 'like', "%{$search}%");
            });
        }

        $trainings = $query->orderBy('created_at', 'desc')->paginate(15);

        $departments = Divisi::all();

        return view('hr.training.index', compact('trainings', 'departments'));
    }

    /**
     * Show the form for creating a new training
     */
    public function create()
    {
        $departments = Divisi::all();
        $users = User::all();

        return view('hr.training.create', compact('departments', 'users'));
    }

    /**
     * Store a newly created training
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'training_name' => 'required|string|max:255',
            'training_type' => 'required|in:mandatory,optional,certification,skill_development',
            'training_method' => 'required|in:classroom,online,hybrid,workshop,seminar',
            'target_departments' => 'nullable|array',
            // 'target_departments.*' => 'exists:tb_divisis,id',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $training = TrainingMaster::create([
                'training_name' => $request->training_name,
                'training_type' => $request->training_type,
                'training_method' => $request->training_method,
                'target_departments' => $request->target_departments,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'status' => 'active',
                'training_code' => 'TRN-' . str_pad(TrainingMaster::count() + 1, 4, '0', STR_PAD_LEFT)
            ]);

            DB::commit();

            return redirect()->route('hr.training.index')
                ->with('success', 'Training berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display training details for modal view
     */
    public function view($id)
    {
        $training = TrainingMaster::with([
            'creator',
            'updater',
            'participants.employee'
        ])->findOrFail($id);

        $participants = $training->participants()
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('hr.training.modal.view', compact('training', 'participants'));
    }

    /**
     * Show the form for editing the specified training (for modal)
     */
    public function edit($id)
    {
        $training = TrainingMaster::with(['creator', 'participants'])->findOrFail($id);
        $departments = Divisi::all();
        $users = User::all();

        return view('hr.training.modal.edit', compact('training', 'departments', 'users'));
    }

    /**
     * Update the specified training
     */
    public function update(Request $request, $id)
    {
        $training = TrainingMaster::findOrFail($id);
        // dd($training);

        // $validator = Validator::make($request->all(), [
        //     'training_name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'objectives' => 'nullable|string',
        //     'prerequisites' => 'nullable|string',
        //     'training_type' => 'required|in:mandatory,optional,certification,skill_development',
        //     'training_method' => 'required|in:classroom,online,hybrid,workshop,seminar',
        //     'duration_hours' => 'required|integer|min:1',
        //     'max_participants' => 'nullable|integer|min:1',
        //     'min_participants' => 'required|integer|min:1',
        //     'cost_per_participant' => 'nullable|numeric|min:0',
        //     'instructor_name' => 'nullable|string|max:255',
        //     'instructor_contact' => 'nullable|string|max:255',
        //     'status' => 'required|in:active,inactive',
        //     'is_active' => 'boolean',
        //     'target_departments' => 'nullable|array',
        //     // 'target_departments.*' => 'exists:tb_divisis,id',
        //     'target_positions' => 'nullable|array',
        //     // 'target_positions.*' => 'exists:tb_jabatans,id',
        //     'target_levels' => 'nullable|array',
        //     // 'target_levels.*' => 'exists:tb_levels,id',
        //     'notes' => 'nullable|string'
        // ]);

        // if ($validator->fails()) {
        //     return redirect()->back()
        //         ->withErrors($validator)
        //         ->withInput();
        // }

        DB::beginTransaction();
        try {
        $training->update([
            'training_name' => $request->training_name,
            'training_type' => $request->training_type,
            'training_method' => $request->training_method,
            'status' => $request->status,
            'target_departments' => $request->target_departments,
            'notes' => $request->notes,
            'updated_by' => Auth::id()
        ]);

            // Update training departments
            // if ($request->has('training_departments')) {
            //     // Delete existing departments
            //     $training->departments()->delete();

            //     // Create new departments
            //     foreach ($request->training_departments as $deptData) {
            //         TrainingDepartment::create([
            //             'training_id' => $training->id,
            //             'department_id' => $deptData['department_id'],
            //             'is_mandatory' => $deptData['is_mandatory'] ?? false,
            //             'priority' => $deptData['priority'] ?? 1,
            //             'notes' => $deptData['notes'] ?? null
            //         ]);
            //     }
            // }

            DB::commit();

            return redirect()->route('hr.training.index')
                ->with('success', 'Training berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified training
     */
    public function destroy($id)
    {
        $training = TrainingMaster::findOrFail($id);

        // Check if training has participants
        if ($training->participants()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus training yang sudah memiliki peserta.');
        }

        $training->delete();

        return redirect()->route('hr.training.index')
            ->with('success', 'Training berhasil dihapus.');
    }

    /**
     * Publish training
     */
    public function publish($id)
    {
        $training = TrainingMaster::findOrFail($id);

        $training->update([
            'status' => 'active',
            'updated_by' => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Training berhasil dipublikasikan.');
    }

    /**
     * Get available trainings for employee
     */
    public function getAvailableTrainings(Request $request)
    {
        $employee = Auth::user();

        $trainings = TrainingMaster::published()
            ->active()
            ->forDepartment($employee->divisi)
            ->whereDoesntHave('participants', function($query) use ($employee) {
                $query->where('employee_id', $employee->id)
                      ->whereIn('registration_status', ['registered', 'approved', 'attended', 'completed']);
            })
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($trainings);
    }

    /**
     * Get employee training history by department (API)
     */
    public function getEmployeeHistory(Request $request)
    {
        try {
            $departmentId = $request->get('department_id');

            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Department ID is required'
                ], 400);
            }

            // Get employees from the department
            $employees = User::where('divisi', $departmentId)
                ->with(['jabatanUser', 'levelUser'])
                ->get();

            $result = [];

            foreach ($employees as $employee) {
                // Get training history for this employee
                $trainingHistory = TrainingParticipant::where('employee_id', $employee->id)
                    ->with(['training'])
                    ->get();

                $trainings = [];
                foreach ($trainingHistory as $participant) {
                    if ($participant->training) {
                        $trainings[] = [
                            'training_name' => $participant->training->training_name,
                            'registration_status' => $participant->registration_status,
                            'attendance_status' => $participant->attendance_status,
                            'training_date' => $participant->created_at ? $participant->created_at->format('d/m/Y') : null
                        ];
                    }
                }

                $result[] = [
                    'Nama' => $employee->name,
                    'Nip' => $employee->nip ?? '-',
                    'position' => $employee->jabatanUser->jabatan ?? '-',
                    'trainings' => $trainings
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
