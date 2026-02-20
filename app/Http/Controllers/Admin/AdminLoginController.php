<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function create()
    {
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($request->input('email')).'|'.$request->ip());

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['email' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)])])->withInput();
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            return back()->withErrors(['email' => trans('auth.failed')])->withInput();
        }

        $user = Auth::user();

        // Sécurité : Vérification stricte du rôle Admin
        if (!$user || !($user->is_admin ?? false)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            return back()->withErrors(['email' => 'Accès refusé. Ce compte ne possède pas les droits d\'administration.'])->withInput();
        }

        // Sécurité : Vérification compte bloqué
        if ($user->is_blocked ?? false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            return back()->withErrors(['email' => 'Votre compte administrateur est suspendu.'])->withInput();
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        // Sécurité : Session unique (déconnecte les autres appareils)
        $currentId = $request->session()->getId();
        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '<>', $currentId)
            ->delete();

        return redirect()->intended(route('admin.dashboard'));
    }
}
