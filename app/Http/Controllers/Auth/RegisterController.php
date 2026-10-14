<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm(Request $request)
    {
        abort_unless($request->user()->hasRole('SuperAdmin'), 403);

        $karyawan = DB::table('karyawan')
            ->leftJoin('users', 'karyawan.id_karyawan', '=', 'users.id_karyawan')
            ->whereNull('users.id')
            ->select('karyawan.id_karyawan', 'karyawan.nama_karyawan')
            ->orderBy('karyawan.id_karyawan')
            ->get();

        return view('auth.register', compact('karyawan'));
    }

    /**
     * Register a new user with the default role.
     */
    public function register(Request $request)
    {
        abort_unless($request->user()->hasRole('SuperAdmin'), 403);

        $request->validate([
            'id_karyawan' => 'required|exists:karyawan,id_karyawan|unique:users,id_karyawan',
            'email' => 'required|email|max:255|ends_with:@dty.local|unique:users,email',
        ]);

        $karyawan = DB::table('karyawan')->where('id_karyawan', $request->id_karyawan)->first();

        $user = User::create([
            'id_karyawan' => $karyawan->id_karyawan,
            'name' => $karyawan->nama_karyawan,
            'email' => $request->email,
            'password' => 'password123@',
        ]);

        $user->assignRole('User');

        return redirect()->route('register')->with('success', 'User '.$user->name.' berhasil ditambahkan dengan role User.');
    }
}
