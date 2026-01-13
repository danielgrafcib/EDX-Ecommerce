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

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => trans('auth.failed')])->withInput();
        }

        $request->session()->regenerate();

        $currentId = $request->session()->getId();
        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('id', '<>', $currentId)
            ->delete();

        if (! ($request->user()->is_admin ?? false)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403);
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
