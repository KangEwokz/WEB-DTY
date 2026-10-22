<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Pemakaian di route: ->middleware('area:VIS') atau 'area:VIS,LAB'
// Hanya membatasi operator; area diambil dari helper area_aktif().
class CekArea
{
    public function handle(Request $request, Closure $next, ...$area)
    {
        if (auth()->user()->hasRole('operator') && ! in_array(area_aktif(), $area)) {
            abort(403, 'Menu ini bukan untuk area kerja Anda hari ini.');
        }

        return $next($request);
    }
}
