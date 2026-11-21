<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;

/** Log aktivitas sistem — hanya Superadmin. */
class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::query()->with('user')->latest();

        if ($request->filled('aksi')) {
            $query->where('aksi', 'like', '%'.$request->input('aksi').'%');
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->integer('user'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->input('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->input('sampai'));
        }

        return view('dashboard.log.index', [
            'log' => $query->paginate(30)->withQueryString(),
            'pengguna' => User::query()->orderBy('name')->get(['id', 'name', 'role']),
            'filter' => $request->only(['aksi', 'user', 'dari', 'sampai']),
        ]);
    }
}
