<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;

class CustomerAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('is_admin', false);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $customers = $query->latest()->paginate(20);

        return view('admin.customers.index', compact('customers','search'));
    }

    public function show(int $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);
        $orders = $customer->orders()->latest()->with('items.product')->paginate(10);
        $addresses = $customer->addresses()->latest()->get();

        return view('admin.customers.show', compact('customer','orders','addresses'));
    }

    public function block(int $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);
        $customer->is_blocked = true;
        $customer->save();
        return redirect()->route('admin.customers.show', $customer->id)->with('status', 'Client bloqué.');
    }

    public function unblock(int $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);
        $customer->is_blocked = false;
        $customer->save();
        return redirect()->route('admin.customers.show', $customer->id)->with('status', 'Client débloqué.');
    }

    public function destroy(int $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('status', 'Client supprimé.');
    }

    public function resetPassword(int $id)
    {
        $customer = User::where('is_admin', false)->findOrFail($id);
        Password::sendResetLink(['email' => $customer->email]);
        return back()->with('status', 'Email de réinitialisation envoyé.');
    }

    public function export()
    {
        $filename = 'customers_'.now()->format('Ymd_His').'.csv';
        $rows = User::where('is_admin', false)->withCount('orders')->latest()->get(['id','name','email','created_at']);
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','name','email','orders_count','created_at']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->id, $r->name, $r->email, $r->orders_count, optional($r->created_at)->format('Y-m-d H:i:s')]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }
}



















