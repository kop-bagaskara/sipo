<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\IshiharaPlate;
use Illuminate\Http\Request;

class IshiharaPlateController extends Controller
{
    /**
     * Display a listing of Ishihara plates.
     */
    public function index(Request $request)
    {
        $query = IshiharaPlate::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('correct_answer', 'like', "%{$search}%")
                  ->orWhere('image_path', 'like', "%{$search}%");
            });
        }

        // Filter by difficulty level
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->difficulty_level);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Order by display_order
        $query->orderBy('display_order', 'asc')->orderBy('plate_number', 'asc');

        $plates = $query->paginate(30);

        // Statistics
        $totalPlates = IshiharaPlate::count();
        $activePlates = IshiharaPlate::where('is_active', true)->count();
        $inactivePlates = IshiharaPlate::where('is_active', false)->count();

        return view('hr.ishihara-plates.index', compact('plates', 'totalPlates', 'activePlates', 'inactivePlates'));
    }
}

