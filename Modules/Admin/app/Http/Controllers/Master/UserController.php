<?php

namespace Modules\Admin\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\User;

// UC04 Kelola Akun Pengguna
class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('users')
            ->join('karyawan', 'users.id_karyawan', '=', 'karyawan.id_karyawan')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'karyawan.nama_karyawan', 'roles.name as role');

        if ($request->cari) {
            $query->where(function ($q) use ($request) {
                $q->where('users.username', 'like', '%'.$request->cari.'%')
                    ->orWhere('karyawan.nama_karyawan', 'like', '%'.$request->cari.'%');
            });
        }
        if ($request->role) {
            $query->where('roles.name', $request->role);
        }

        $user = $query->orderBy('users.username')->paginate(10)->withQueryString();
        $role = DB::table('roles')->where('name', '!=', 'superadmin')->orderBy('name')->pluck('name');

        return view('admin::master.user.index', compact('user', 'role'));
    }

    public function create()
    {
        // Hanya karyawan aktif yang belum punya akun (satu karyawan satu akun)
        $karyawan = DB::table('karyawan')
            ->leftJoin('users', 'karyawan.id_karyawan', '=', 'users.id_karyawan')
            ->whereNull('users.id')
            ->where('karyawan.status_aktif', 'Aktif')
            ->select('karyawan.id_karyawan', 'karyawan.nama_karyawan')
            ->orderBy('karyawan.nama_karyawan')
            ->get();
        $role = DB::table('roles')->where('name', '!=', 'superadmin')->orderBy('name')->pluck('name');

        return view('admin::master.user.create', compact('karyawan', 'role'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // karyawan harus Aktif dan belum punya akun
            'id_karyawan' => 'required|exists:karyawan,id_karyawan,status_aktif,Aktif|unique:users,id_karyawan',
            'username' => 'required|max:20|alpha_dash|unique:users,username',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|exists:roles,name|not_in:superadmin',
        ]);

        $id = DB::table('users')->insertGetId([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'id_karyawan' => $request->id_karyawan,
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Role disimpan Spatie di tabel model_has_roles
        User::find($id)->assignRole($request->role);

        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = DB::table('users')
            ->join('karyawan', 'users.id_karyawan', '=', 'karyawan.id_karyawan')
            ->where('users.id', $id)
            ->select('users.*', 'karyawan.nama_karyawan')
            ->first();
        if (! $user) {
            abort(404);
        }
        $user->role = User::find($id)->getRoleNames()->first();
        $role = DB::table('roles')->where('name', '!=', 'superadmin')->orderBy('name')->pluck('name');

        return view('admin::master.user.edit', compact('user', 'role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|max:20|alpha_dash|unique:users,username,'.$id,
            'password' => 'nullable|min:6|confirmed',   // kosong = password tidak diubah
            'role' => 'required|exists:roles,name|not_in:superadmin',
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        if ($id == auth()->id() && $request->status == 'Non-Aktif') {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $data = [
            'username' => $request->username,
            'status' => $request->status,
            'updated_at' => now(),
        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        DB::table('users')->where('id', $id)->update($data);

        User::find($id)->syncRoles([$request->role]);

        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil diubah.');
    }

    // Akun tidak dihapus, cukup dinonaktifkan
    public function destroy($id)
    {
        if ($id == auth()->id()) {
            return redirect()->route('admin.user.index')->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        DB::table('users')->where('id', $id)->update([
            'status' => 'Non-Aktif',
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Akun pengguna berhasil dinonaktifkan.');
    }
}
