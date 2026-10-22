<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Pemakaian di route: ->middleware('divisi:QCT') atau 'divisi:QCT,Produksi'
class CekDivisi
{
    public function handle(Request $request, Closure $next, ...$divisi)
    {
        // Direktur tidak terikat divisi (boleh membuka rekap check QCT), begitu juga superadmin (demo).
        // Batas menunya tetap diatur oleh middleware permission.
        if (auth()->user()->hasAnyRole(['direktur', 'superadmin'])) {
            return $next($request);
        }

        if (! in_array(divisi_user(), $divisi)) {
            abort(403, 'Menu ini bukan untuk divisi Anda.');
        }

        return $next($request);
    }
}
