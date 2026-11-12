<?php

use Illuminate\Foundation\Application;                           // class utama aplikasi Laravel
use Illuminate\Foundation\Configuration\Exceptions;              // pengaturan penanganan error
use Illuminate\Foundation\Configuration\Middleware;              // pengaturan middleware
use Illuminate\Http\Request;                                     // Request = data permintaan dari browser
use Modules\Auth\Http\Middleware\CekAkses;                       // panggil middleware CekAkses buatan kita

return Application::configure(basePath: dirname(__DIR__))        // nyalakan aplikasi dari folder project
    ->withRouting(                                               // daftarkan file-file route
        web: __DIR__.'/../routes/web.php',                       // route web utama (kosong, route ada di modul)
        commands: __DIR__.'/../routes/console.php',              // perintah artisan tambahan
        health: '/up',                                           // URL /up untuk cek server hidup
    )
    ->withMiddleware(function (Middleware $middleware): void {   // pengaturan middleware
        $middleware->alias([                                     // daftarkan nama pendek middleware
            'akses' => CekAkses::class,                          // 'akses' = CekAkses, dipakai di route: ->middleware('akses')
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {   // pengaturan error
        $exceptions->shouldRenderJsonWhen(                       // kapan error dikirim dalam bentuk JSON
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(), // jika URL diawali api/ atau request meminta JSON
        );
    })->create();                                                // buat aplikasinya
