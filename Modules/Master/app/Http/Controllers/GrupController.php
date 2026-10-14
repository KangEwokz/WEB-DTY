<?php

namespace Modules\Master\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GrupController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:grup.view', only: ['index']),
            new Middleware('permission:grup.create', only: ['create', 'store']),
            new Middleware('permission:grup.edit', only: ['edit', 'update']),
            new Middleware('permission:grup.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $grup = DB::table('grup')->orderBy('id_grup')->get();

        return view('master::grup.index', compact('grup'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master::grup.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_grup' => 'required|string|max:10|unique:grup,id_grup',
            'nm_grup' => 'required|string|max:20',
        ]);

        DB::table('grup')->insert([
            'id_grup' => $request->id_grup,
            'nm_grup' => $request->nm_grup,
        ]);

        return redirect()->route('grup.index')->with('success', 'Data grup berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id_grup)
    {
        $grup = DB::table('grup')->where('id_grup', $id_grup)->firstOrFail();

        return view('master::grup.edit', compact('grup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id_grup)
    {
        DB::table('grup')->where('id_grup', $id_grup)->firstOrFail();

        $request->validate([
            'id_grup' => ['required', 'string', 'max:10', Rule::unique('grup', 'id_grup')->ignore($id_grup, 'id_grup')],
            'nm_grup' => 'required|string|max:20',
        ]);

        DB::table('grup')->where('id_grup', $id_grup)->update([
            'id_grup' => $request->id_grup,
            'nm_grup' => $request->nm_grup,
        ]);

        return redirect()->route('grup.index')->with('success', 'Data grup berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id_grup)
    {
        DB::table('grup')->where('id_grup', $id_grup)->firstOrFail();

        if (DB::table('karyawan')->where('id_grup', $id_grup)->exists()) {
            return redirect()->route('grup.index')->with('error', 'Data grup tidak dapat dihapus karena masih digunakan oleh karyawan.');
        }

        DB::table('grup')->where('id_grup', $id_grup)->delete();

        return redirect()->route('grup.index')->with('success', 'Data grup berhasil dihapus.');
    }
}
