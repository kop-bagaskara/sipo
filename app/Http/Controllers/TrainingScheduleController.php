<?php

namespace App\Http\Controllers;

use App\Models\TrainingMaster;
use App\Models\TrainingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TrainingScheduleController extends Controller
{
    /**
     * Display training schedules
     */
    public function index(Request $request)
    {
        $query = TrainingSchedule::with(['training', 'creator']);

        // Filter by training
        if ($request->has('training_id') && $request->training_id !== '') {
            $query->where('training_id', $request->training_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->where('schedule_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->where('schedule_date', '<=', $request->date_to);
        }

        $schedules = $query->orderBy('schedule_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(20);

        // Get filter options
        $trainings = TrainingMaster::where('status', 'active')->get();
        // dd($trainings);

        return view('hr.training.schedule.index', compact('schedules', 'trainings'));
    }

    /**
     * Show create schedule form
     */
    public function create(Request $request)
    {
        $trainingId = $request->get('training_id');
        $training = null;

        if ($trainingId) {
            $training = TrainingMaster::findOrFail($trainingId);
        }

        $trainings = TrainingMaster::where('status', 'active')->get();

        return view('hr.training.schedule.create', compact('trainings', 'training'));
    }

    /**
     * Store new schedule
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'training_id' => 'required|exists:tb_training_masters,id',
            'schedule_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        // dd($request->all());

        try {
            $schedule = TrainingSchedule::create([
                'training_id' => $request->training_id,
                'schedule_date' => $request->schedule_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'description' => $request->description,
                'status' => 'scheduled',
                'created_by' => Auth::id()
            ]);

            // Update training status to active if it has participants and schedule
            // $training = TrainingMaster::find($request->training_id);
            // if ($training->participants()->count() > 0 && $training->status === 'published') {
            //     $training->update(['status' => 'active']);
            // }

            DB::commit();

            return redirect()->route('hr.training.schedule.index')
                ->with('success', 'Jadwal training berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show schedule details
     */
    public function show($id)
    {
        $schedule = TrainingSchedule::with(['training.participants.employee', 'creator'])
            ->findOrFail($id);

        return view('hr.training.schedule.show', compact('schedule'));
    }

    /**
     * Show edit schedule form
     */
    public function edit($id)
    {
        $schedule = TrainingSchedule::findOrFail($id);
        $trainings = TrainingMaster::where('status', 'published')->get();

        return view('hr.training.schedule.edit', compact('schedule', 'trainings'));
    }

    /**
     * Update schedule
     */
    public function update(Request $request, $id)
    {
        $schedule = TrainingSchedule::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $schedule->update($request->only([
            'schedule_date',
            'start_time',
            'end_time',
            'location',
            'description',
            'status'
        ]));

        return redirect()->route('hr.training.schedule.index')
            ->with('success', 'Jadwal training berhasil diperbarui.');
    }

    /**
     * Delete schedule
     */
    public function destroy($id)
    {
        $schedule = TrainingSchedule::findOrFail($id);

        // Check if schedule can be deleted
        if ($schedule->status === 'ongoing') {
            return redirect()->back()
                ->with('error', 'Tidak dapat menghapus jadwal yang sedang berlangsung.');
        }

        $schedule->delete();

        return redirect()->route('hr.training.schedule.index')
            ->with('success', 'Jadwal training berhasil dihapus.');
    }

    /**
     * Get upcoming schedules for dashboard
     */
    public function getUpcoming()
    {
        $schedules = TrainingSchedule::with(['training'])
            ->where('schedule_date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('schedule_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(10)
            ->get();

        return response()->json($schedules);
    }
}
