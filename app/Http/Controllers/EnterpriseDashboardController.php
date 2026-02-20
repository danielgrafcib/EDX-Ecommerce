<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Enterprise;

class EnterpriseDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch enterprises where user is owner OR has a role via pivot
        $enterprises = Enterprise::whereHas('users', function($q) use ($user) {
            $q->where('users.id', $user->id);
        })->with('wallet')->get();
        
        // Also merge those where user_id is the owner directly (if not in pivot)
        $ownedEnterprises = Enterprise::where('user_id', $user->id)->with('wallet')->get();
        
        $allEnterprises = $enterprises->merge($ownedEnterprises)->unique('id');

        return view('enterprise.dashboard', ['enterprises' => $allEnterprises]);
    }

    public function manage($id)
    {
        $user = Auth::user();
        $enterprise = Enterprise::with(['wallet.transactions', 'bookings.service', 'bookings.user'])
            ->findOrFail($id);

        // Check permission (Owner or linked user)
        $isLinked = Enterprise::where('id', $enterprise->id)
            ->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->exists();

        if ($enterprise->user_id !== $user->id && !$isLinked) {
            abort(403, 'Unauthorized action.');
        }

        return view('enterprise.manage', compact('enterprise'));
    }
}
