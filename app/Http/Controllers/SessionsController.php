<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required' 
        ]);

        if (Auth::attempt($attributes)) {
            if (Auth::user()->is_active === false) {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun admin Anda tidak aktif.']);
            }

            session()->regenerate();
            app(ActivityLogger::class)->log('login', 'Auth', 'Admin berhasil login.');
            return redirect('/paneladmin')->with(['success' => 'You are logged in.']);
        }

        return back()->withErrors(['email' => 'Email or password invalid.']);
    }

    public function destroy(Request $request)
    {
        app(ActivityLogger::class)->log('logout', 'Auth', 'Admin logout.');
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('paneladmin.login')->with(['success' => 'You\'ve been logged out.']);
    }
}
