<?php

namespace Modules\Karyawan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:karyawan.view'),
        ];
    }

    public function index()
    {
        return view('karyawan::dashboard');
    }
}
