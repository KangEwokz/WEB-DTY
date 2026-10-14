<?php

namespace Modules\Karyawan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:karyawan.view', only: ['index', 'show']),
            new Middleware('permission:karyawan.create', only: ['create', 'store']),
            new Middleware('permission:karyawan.edit', only: ['edit', 'update']),
            new Middleware('permission:karyawan.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $karyawan = DB::table('karyawan')
            ->join('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')
            ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->join('grup', 'karyawan.id_grup', '=', 'grup.id_grup')
            ->select('karyawan.*', 'divisi.nm_divisi', 'jabatan.nm_jabatan', 'grup.nm_grup')
            ->when($request->cari, function ($query, $cari) {
                $query->where(function ($query) use ($cari) {
                    $query->where('karyawan.id_karyawan', 'like', '%'.$cari.'%')
                        ->orWhere('karyawan.nama_karyawan', 'like', '%'.$cari.'%');
                });
            })
            ->when($request->id_divisi, function ($query, $id_divisi) {
                $query->where('karyawan.id_divisi', $id_divisi);
            })
            ->when($request->id_jabatan, function ($query, $id_jabatan) {
                $query->where('karyawan.id_jabatan', $id_jabatan);
            })
            ->when($request->id_grup, function ($query, $id_grup) {
                $query->where('karyawan.id_grup', $id_grup);
            })
            ->orderBy('karyawan.id_karyawan')
            ->paginate(10)
            ->withQueryString();

        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get();
        $grup = DB::table('grup')->orderBy('id_grup')->get();

        return view('karyawan::index', compact('karyawan', 'divisi', 'jabatan', 'grup'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get();
        $grup = DB::table('grup')->orderBy('id_grup')->get();

        return view('karyawan::create', compact('divisi', 'jabatan', 'grup'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_karyawan' => 'required|string|max:20|unique:karyawan,id_karyawan',
            'nama_karyawan' => 'required|string|max:50',
            'jns_kelamin' => 'required|in:L,P',
            'departemen' => 'required|in:DTY',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_grup' => 'required|exists:grup,id_grup',
        ]);

        DB::table('karyawan')->insert([
            'id_karyawan' => $request->id_karyawan,
            'nama_karyawan' => $request->nama_karyawan,
            'jns_kelamin' => $request->jns_kelamin,
            'departemen' => $request->departemen,
            'id_divisi' => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'id_grup' => $request->id_grup,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    /**
     * Show the specified resource.
     */
    public function show(string $id_karyawan)
    {
        $karyawan = DB::table('karyawan')
            ->join('divisi', 'karyawan.id_divisi', '=', 'divisi.id_divisi')
            ->join('jabatan', 'karyawan.id_jabatan', '=', 'jabatan.id_jabatan')
            ->join('grup', 'karyawan.id_grup', '=', 'grup.id_grup')
            ->leftJoin('users', 'karyawan.id_karyawan', '=', 'users.id_karyawan')
            ->select('karyawan.*', 'divisi.nm_divisi', 'jabatan.nm_jabatan', 'grup.nm_grup', 'users.email')
            ->where('karyawan.id_karyawan', $id_karyawan)
            ->firstOrFail();

        return view('karyawan::show', compact('karyawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_karyawan)
    {
        $karyawan = DB::table('karyawan')->where('id_karyawan', $id_karyawan)->firstOrFail();

        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get();
        $grup = DB::table('grup')->orderBy('id_grup')->get();

        return view('karyawan::edit', compact('karyawan', 'divisi', 'jabatan', 'grup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_karyawan)
    {
        DB::table('karyawan')->where('id_karyawan', $id_karyawan)->firstOrFail();

        $request->validate([
            'id_karyawan' => ['required', 'string', 'max:20', Rule::unique('karyawan', 'id_karyawan')->ignore($id_karyawan, 'id_karyawan')],
            'nama_karyawan' => 'required|string|max:50',
            'jns_kelamin' => 'required|in:L,P',
            'departemen' => 'required|in:DTY',
            'id_divisi' => 'required|exists:divisi,id_divisi',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'id_grup' => 'required|exists:grup,id_grup',
        ]);

        DB::table('karyawan')->where('id_karyawan', $id_karyawan)->update([
            'id_karyawan' => $request->id_karyawan,
            'nama_karyawan' => $request->nama_karyawan,
            'jns_kelamin' => $request->jns_kelamin,
            'departemen' => $request->departemen,
            'id_divisi' => $request->id_divisi,
            'id_jabatan' => $request->id_jabatan,
            'id_grup' => $request->id_grup,
        ]);

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_karyawan)
    {
        DB::table('karyawan')->where('id_karyawan', $id_karyawan)->firstOrFail();

        if (DB::table('users')->where('id_karyawan', $id_karyawan)->exists()) {
            return redirect()->route('karyawan.index')->with('error', 'Data karyawan tidak dapat dihapus karena masih memiliki akun user.');
        }

        DB::table('karyawan')->where('id_karyawan', $id_karyawan)->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
