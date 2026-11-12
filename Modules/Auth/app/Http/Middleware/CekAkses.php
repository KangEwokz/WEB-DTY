<?php

namespace Modules\Auth\Http\Middleware;                          // "alamat" class ini = folder Modules/Auth/app/Http/Middleware

use App\Models\User;                                             // model User (punya fungsi punyaAkses)
use Closure;                                                     // Closure = fungsi "lanjut ke proses berikutnya" ($next)
use Illuminate\Http\Request;                                     // Request = data permintaan dari browser
use Illuminate\Support\Facades\Auth;                             // Auth = untuk mengambil user yang sedang login

class CekAkses                                                   // middleware = penjaga pintu sebelum masuk controller; dipakai di route: ->middleware('akses')
{
    public function handle(Request $request, Closure $next)      // otomatis dijalankan Laravel setiap route yang memakai middleware ini dibuka
    {
        $user = User::find(Auth::id());                          // ambil data user yang sedang login (Auth::id() = id user)
        $nama_route = $request->route()->getName();              // ambil nama route yang sedang dibuka, contoh 'admin.karyawan.create'

        if (! $user->punyaAkses($nama_route)) {                  // jika user TIDAK punya akses ke route ini
            abort(403, 'Anda tidak punya akses ke halaman ini.'); // hentikan + tampilkan halaman error 403 (dilarang)
        }                                                        // akhir if

        return $next($request);                                  // punya akses -> lanjut ke controller
    }
}
