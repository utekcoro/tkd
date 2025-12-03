<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function login_proses(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Semua role diarahkan ke halaman pilih cabang
            return redirect()->route('branch.select');
        }

        return redirect()->route('login')->with('error', 'Username atau Password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logout berhasil');
    }

    public function switchBranch(Request $request)
    {
        $user = Auth::user();
        $branchId = $request->branch_id;

        if (!in_array($user->role, ['super_admin', 'owner'])) {
            if (!$user->branches->pluck('id')->contains($branchId)) {
                return back()->with('error', 'Anda tidak punya akses ke cabang ini');
            }
        }

        session(['active_branch' => $branchId]);
        return back()->with('success', 'Cabang aktif diganti');
    }
}
