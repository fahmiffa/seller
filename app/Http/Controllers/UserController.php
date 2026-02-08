<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Check if user has admin role (role = 0)
     */
    private function checkAdminAccess()
    {
        if (auth()->user()->role != 0) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkAdminAccess();
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkAdminAccess();
        $parents = User::where('role', 0)->orWhere('role', 1)->get();
        return view('users.create', compact('parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdminAccess();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|in:0,1,3',
            'saldo' => 'required|numeric|min:0',
            'limit' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'parent_id' => 'nullable|exists:users,id',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->checkAdminAccess();
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->checkAdminAccess();
        $parents = User::where('id', '!=', $user->id)->get();
        return view('users.edit', compact('user', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdminAccess();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|confirmed',
            'role' => 'required|integer|in:0,1,3',
            'saldo' => 'required|numeric|min:0',
            'limit' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'parent_id' => 'nullable|exists:users,id',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->checkAdminAccess();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
