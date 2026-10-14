<?php

namespace Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JabatanController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:jabatan.view', only: ['index']),
            new Middleware('permission:jabatan.create', only: ['create', 'store']),
            new Middleware('permission:jabatan.edit', only: ['edit', 'update']),
            new Middleware('permission:jabatan.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jabatan = DB::table('jabatan')->orderBy('id_jabatan')->get();

        return view('master::jabatan.index', compact('jabatan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master::jabatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_jabatan' => 'required|string|max:10|unique:jabatan,id_jabatan',
            'nm_jabatan' => 'required|string|max:50',
        ]);

        DB::table('jabatan')->insert([
            'id_jabatan' => $request->id_jabatan,
            'nm_jabatan' => $request->nm_jabatan,
        ]);

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_jabatan)
    {
        $jabatan = DB::table('jabatan')->where('id_jabatan', $id_jabatan)->firstOrFail();

        return view('master::jabatan.edit', compact('jabatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_jabatan)
    {
        DB::table('jabatan')->where('id_jabatan', $id_jabatan)->firstOrFail();

        $request->validate([
            'id_jabatan' => ['required', 'string', 'max:10', Rule::unique('jabatan', 'id_jabatan')->ignore($id_jabatan, 'id_jabatan')],
            'nm_jabatan' => 'required|string|max:50',
        ]);

        DB::table('jabatan')->where('id_jabatan', $id_jabatan)->update([
            'id_jabatan' => $request->id_jabatan,
            'nm_jabatan' => $request->nm_jabatan,
        ]);

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_jabatan)
    {
        DB::table('jabatan')->where('id_jabatan', $id_jabatan)->firstOrFail();

        if (DB::table('karyawan')->where('id_jabatan', $id_jabatan)->exists()) {
            return redirect()->route('jabatan.index')->with('error', 'Data jabatan tidak dapat dihapus karena masih digunakan oleh karyawan.');
        }

        DB::table('jabatan')->where('id_jabatan', $id_jabatan)->delete();

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil dihapus.');
    }
}
