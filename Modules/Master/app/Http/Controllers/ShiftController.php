<?php

namespace Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ShiftController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:shift.view', only: ['index']),
            new Middleware('permission:shift.create', only: ['create', 'store']),
            new Middleware('permission:shift.edit', only: ['edit', 'update']),
            new Middleware('permission:shift.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shift = DB::table('shift')->orderBy('kode_shift')->get();

        return view('master::shift.index', compact('shift'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master::shift.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_shift' => 'required|string|max:10|unique:shift,kode_shift',
            'nama_shift' => 'required|string|max:50',
            'jam_masuk' => 'nullable|date_format:H:i|required_with:jam_pulang',
            'jam_pulang' => 'nullable|date_format:H:i|required_with:jam_masuk',
        ]);

        DB::table('shift')->insert([
            'kode_shift' => $request->kode_shift,
            'nama_shift' => $request->nama_shift,
            'jam_masuk' => $request->jam_masuk ?: null,
            'jam_pulang' => $request->jam_pulang ?: null,
        ]);

        return redirect()->route('shift.index')->with('success', 'Data shift berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $kode_shift)
    {
        $shift = DB::table('shift')->where('kode_shift', $kode_shift)->firstOrFail();

        return view('master::shift.edit', compact('shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $kode_shift)
    {
        DB::table('shift')->where('kode_shift', $kode_shift)->firstOrFail();

        $request->validate([
            'kode_shift' => ['required', 'string', 'max:10', Rule::unique('shift', 'kode_shift')->ignore($kode_shift, 'kode_shift')],
            'nama_shift' => 'required|string|max:50',
            'jam_masuk' => 'nullable|date_format:H:i|required_with:jam_pulang',
            'jam_pulang' => 'nullable|date_format:H:i|required_with:jam_masuk',
        ]);

        DB::table('shift')->where('kode_shift', $kode_shift)->update([
            'kode_shift' => $request->kode_shift,
            'nama_shift' => $request->nama_shift,
            'jam_masuk' => $request->jam_masuk ?: null,
            'jam_pulang' => $request->jam_pulang ?: null,
        ]);

        return redirect()->route('shift.index')->with('success', 'Data shift berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kode_shift)
    {
        DB::table('shift')->where('kode_shift', $kode_shift)->firstOrFail();

        DB::table('shift')->where('kode_shift', $kode_shift)->delete();

        return redirect()->route('shift.index')->with('success', 'Data shift berhasil dihapus.');
    }
}
