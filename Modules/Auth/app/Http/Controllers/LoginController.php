<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    // UC01: tampilkan form login
    public function index()
    {
        return view('auth::login');
    }

    // UC01: proses login dengan username + password
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Hanya akun berstatus Aktif yang boleh login
        $berhasil = Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
            'status' => 'Aktif',
        ]);

        if (! $berhasil) {
            return back()
                ->withErrors(['username' => 'Username atau password salah, atau akun tidak aktif.'])
                ->onlyInput('username');
        }

        // Karyawan yang sudah keluar (Non-Aktif) juga tidak boleh login
        $status_karyawan = DB::table('karyawan')
            ->where('id_karyawan', Auth::user()->id_karyawan)
            ->value('status_aktif');

        if ($status_karyawan != 'Aktif') {
            Auth::logout();

            return back()
                ->withErrors(['username' => 'Karyawan sudah tidak aktif.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // UC02: logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }
}
