<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SecurityMasterController extends Controller
{
    /**
     * Display a listing of security users.
     */
    public function index(Request $request)
    {
        $query = User::where('divisi', 11) // ID divisi security
            ->with(['divisiUser', 'jabatanUser', 'levelUser']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->paginate(15);

        return view('main.hr.security-master.index', compact('users'));
    }

    /**
     * Show the form for creating a new security user.
     */
    public function create()
    {
        return view('main.hr.security-master.create');
    }

    /**
     * Store a newly created security user.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:4|confirmed',
            'jabatan' => 'required|exists:tb_jabatans,id',
            'level' => 'required|exists:tb_levels,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'string_password' => $request->password,
            'divisi' => 11, // ID divisi security
            'jabatan' => $request->jabatan,
            'level' => $request->level,
        ]);

        return redirect()->route('hr.security-master.index')
            ->with('success', 'User security berhasil ditambahkan.');
    }

    /**
     * Display the specified security user.
     */
    public function show(User $securityMaster)
    {
        $securityMaster->load(['divisiUser', 'jabatanUser', 'levelUser']);
        return view('main.hr.security-master.show', compact('securityMaster'));
    }

    /**
     * Show the form for editing the specified security user.
     */
    public function edit(User $securityMaster)
    {
        $securityMaster->load(['divisiUser', 'jabatanUser', 'levelUser']);
        return view('main.hr.security-master.edit', compact('securityMaster'));
    }

    /**
     * Update the specified security user.
     */
    public function update(Request $request, User $securityMaster)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($securityMaster->id)],
            'username' => ['required','string','max:255', Rule::unique('users','username')->ignore($securityMaster->id)],
            'jabatan' => 'required|exists:tb_jabatans,id',
            'level' => 'required|exists:tb_levels,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'level' => $request->level,
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $updateData['password'] = Hash::make($request->password);
            $updateData['string_password'] = $request->password;
        }

        $securityMaster->update($updateData);

        return redirect()->route('hr.security-master.index')
            ->with('success', 'User security berhasil diperbarui.');
    }

    /**
     * Remove the specified security user.
     */
    public function destroy(User $securityMaster)
    {
        $securityMaster->delete();

        return redirect()->route('hr.security-master.index')
            ->with('success', 'User security berhasil dihapus.');
    }

}
