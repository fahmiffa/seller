<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\History;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    /** @var User */
    private function checkAdminAccess()
    {
        if (auth()->user()->role != 0) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        }
    }

    public function index()
    {
        $this->checkAdminAccess();
        $users = User::latest()->paginate(10);
        return view('saldos.index', compact('users'));
    }

    public function history()
    {
        $this->checkAdminAccess();
        $histories = History::with('user')->latest()->paginate(15);
        return view('saldos.history', compact('histories'));
    }

    public function topup(Request $request, User $user)
    {
        $this->checkAdminAccess();
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $user->saldo += $validated['amount'];
        $user->save();

        History::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? 'Top up balance',
        ]);

        return back()->with('success', 'Top up berhasil.');
    }
}
