<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = Order::with('user')->latest();
        if ($status) {
            $query->where('status', $status);
        }
        $orders = $query->paginate(20);
        return view('admin.orders.index', compact('orders','status'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,shipped,delivered,cancelled'],
        ]);
        $order = Order::with('items.product','user')->findOrFail($id);
        $previous = $order->status;
        $order->status = $validated['status'];

        if ($previous !== 'confirmed' && $order->status === 'confirmed' && !(bool)($order->stock_decremented ?? false)) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->stock = max(0, (int)$item->product->stock - (int)$item->quantity);
                        $item->product->save();
                    }
                }
                $order->stock_decremented = true;
                $order->save();
            });
        } else {
            $order->save();
        }
        Log::info('Order status changed', ['order_id' => $order->id, 'status' => $order->status]);
        if ($order->user && $order->user->email) {
            $subject = match ($order->status) {
                'confirmed' => 'Confirmation de votre commande',
                'shipped' => 'Votre commande a été expédiée',
                'delivered' => 'Votre commande a été livrée',
                'cancelled' => 'Votre commande a été annulée',
                default => 'Mise à jour de votre commande',
            };
            $body = 'Commande #'.str_pad($order->id,5,'0',STR_PAD_LEFT).' — Statut: '.$order->status;
            if ($order->status === 'confirmed') {
                $lines = [];
                foreach ($order->items as $it) {
                    $lines[] = ($it->product->name ?? ('#'.$it->product_id)).' x'.$it->quantity.' @ '.number_format((float)$it->unit_price,2,',',' ').'€';
                }
                $body .= "\n\nArticles:\n".implode("\n", $lines);
                $body .= "\n\nTotal: ".number_format((float)$order->total,2,',',' ')."€";
            }
            Mail::raw($body, function ($m) use ($order, $subject) {
                $m->to($order->user->email)->subject($subject);
            });
        }
        return redirect()->route('admin.orders.index')->with('status', 'Statut de la commande mis à jour.');
    }

    public function updateTracking(Request $request, int $id)
    {
        $validated = $request->validate([
            'tracking_carrier' => ['nullable', 'string', 'max:120'],
            'tracking_code' => ['nullable', 'string', 'max:180'],
            'tracking_url' => ['nullable', 'url'],
        ]);
        $order = Order::findOrFail($id);
        $order->fill($validated);
        $order->save();
        return redirect()->route('admin.orders.index')->with('status', 'Informations de suivi mises à jour.');
    }

    public function invoice(int $id)
    {
        $order = Order::with('items.product','user')->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    public function export()
    {
        $filename = 'orders_'.now()->format('Ymd_His').'.csv';
        $rows = Order::withCount('items')->latest()->get(['id','user_id','status','payment_status','total','created_at']);
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];
        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id','user_email','status','payment_status','total','items_count','created_at']);
            foreach ($rows as $r) {
                $email = optional($r->user)->email;
                fputcsv($out, [$r->id, $email, $r->status, $r->payment_status, (float)$r->total, $r->items_count, optional($r->created_at)->format('Y-m-d H:i:s')]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }
}
