<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::ResetLinkSent) {
            return back()->with('status', 'Link reset password sudah dikirim ke email Anda.');
        }

        if ($status === Password::ResetThrottled) {
            return back()->withErrors(['email' => 'Tunggu sebentar sebelum meminta link reset lagi.'])->onlyInput('email');
        }

        return back()->withErrors(['email' => 'Email tidak terdaftar.'])->onlyInput('email');
    }
}
