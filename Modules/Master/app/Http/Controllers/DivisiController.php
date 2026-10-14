<?php

namespace Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DivisiController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:divisi.view', only: ['index']),
            new Middleware('permission:divisi.create', only: ['create', 'store']),
            new Middleware('permission:divisi.edit', only: ['edit', 'update']),
            new Middleware('permission:divisi.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $divisi = DB::table('divisi')->orderBy('id_divisi')->get();

        return view('master::divisi.index', compact('divisi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master::divisi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_divisi' => 'required|string|max:10|unique:divisi,id_divisi',
            'nm_divisi' => 'required|string|max:20',
        ]);

        DB::table('divisi')->insert([
            'id_divisi' => $request->id_divisi,
            'nm_divisi' => $request->nm_divisi,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Data divisi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_divisi)
    {
        $divisi = DB::table('divisi')->where('id_divisi', $id_divisi)->firstOrFail();

        return view('master::divisi.edit', compact('divisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_divisi)
    {
        DB::table('divisi')->where('id_divisi', $id_divisi)->firstOrFail();

        $request->validate([
            'id_divisi' => ['required', 'string', 'max:10', Rule::unique('divisi', 'id_divisi')->ignore($id_divisi, 'id_divisi')],
            'nm_divisi' => 'required|string|max:20',
        ]);

        DB::table('divisi')->where('id_divisi', $id_divisi)->update([
            'id_divisi' => $request->id_divisi,
            'nm_divisi' => $request->nm_divisi,
        ]);

        return redirect()->route('divisi.index')->with('success', 'Data divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_divisi)
    {
        DB::table('divisi')->where('id_divisi', $id_divisi)->firstOrFail();

        if (DB::table('karyawan')->where('id_divisi', $id_divisi)->exists()) {
            return redirect()->route('divisi.index')->with('error', 'Data divisi tidak dapat dihapus karena masih digunakan oleh karyawan.');
        }

        DB::table('divisi')->where('id_divisi', $id_divisi)->delete();

        return redirect()->route('divisi.index')->with('success', 'Data divisi berhasil dihapus.');
    }
}
