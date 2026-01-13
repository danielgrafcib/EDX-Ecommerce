@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Commandes</h1>
                <p class="text-sm text-neutral-400">Suivez les commandes et mettez à jour leur statut.</p>
            </div>
            <form method="get" class="flex items-center gap-2">
                <select name="status" class="rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                    <option value="">Tous statuts</option>
                    @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" @selected(($status ?? '') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 rounded-xl border border-neutral-700 text-sm text-neutral-200">Filtrer</button>
            </form>
        </header>

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-2xl border border-neutral-800 bg-neutral-950">
            <table class="min-w-full text-xs text-neutral-300">
                <thead class="border-b border-neutral-800 text-neutral-500">
                <tr>
                    <th class="p-3 text-left">Commande</th>
                    <th class="p-3 text-left">Client</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Statut</th>
                    <th class="p-3 text-left">Total</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr class="border-b border-neutral-900 last:border-0">
                        <td class="p-3">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="p-3">{{ $order->user?->name ?? '—' }}</td>
                        <td class="p-3">{{ $order->created_at?->format('d/m/Y') }}</td>
                        <td class="p-3">
                            <span class="rounded-full bg-neutral-800 px-3 py-1 text-[11px] text-neutral-200">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="p-3">{{ number_format($order->total, 2, ',', ' ') }} €</td>
                        <td class="p-3 text-right space-x-2">
                            <form method="post" action="{{ route('admin.orders.status', $order->id) }}" class="inline-flex items-center gap-2">
                                @csrf
                                @method('put')
                                <select name="status" class="rounded-lg border border-neutral-700 bg-neutral-950 px-2 py-1 text-[12px] text-neutral-50">
                                    @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $s)
                                        <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                <button class="px-3 py-1 rounded-lg border border-neutral-700 text-[11px] text-neutral-200">Mettre à jour</button>
                            </form>
                            <form method="post" action="{{ route('admin.orders.tracking', $order->id) }}" class="mt-2 inline-flex items-center gap-2">
                                @csrf
                                @method('put')
                                <input name="tracking_carrier" value="{{ old('tracking_carrier', $order->tracking_carrier) }}" placeholder="Transporteur" class="w-24 rounded-lg border border-neutral-700 bg-neutral-950 px-2 py-1 text-[12px] text-neutral-50" />
                                <input name="tracking_code" value="{{ old('tracking_code', $order->tracking_code) }}" placeholder="Code" class="w-28 rounded-lg border border-neutral-700 bg-neutral-950 px-2 py-1 text-[12px] text-neutral-50" />
                                <input name="tracking_url" value="{{ old('tracking_url', $order->tracking_url) }}" placeholder="URL suivi" class="w-40 rounded-lg border border-neutral-700 bg-neutral-950 px-2 py-1 text-[12px] text-neutral-50" />
                                <button class="px-3 py-1 rounded-lg border border-neutral-700 text-[11px] text-neutral-200">Suivi</button>
                            </form>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="px-3 py-1 rounded-lg bg-sky-600 text-[11px] font-semibold text-white">Facture</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-neutral-500">Aucune commande.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </section>
@endsection
