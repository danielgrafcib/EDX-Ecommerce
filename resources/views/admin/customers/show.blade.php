@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">{{ $customer->name }}</h1>
                <p class="text-sm text-neutral-400">{{ $customer->email }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200 underline">Retour à la liste</a>
                @if(!$customer->is_blocked)
                    <form method="post" action="{{ route('admin.customers.block', $customer->id) }}">
                        @csrf
                        @method('put')
                        <button class="px-3 py-2 rounded-lg border border-amber-500/50 text-xs text-amber-300">Bloquer</button>
                    </form>
                @else
                    <form method="post" action="{{ route('admin.customers.unblock', $customer->id) }}">
                        @csrf
                        @method('put')
                        <button class="px-3 py-2 rounded-lg border border-emerald-500/50 text-xs text-emerald-200">Débloquer</button>
                    </form>
                @endif
                <form method="post" action="{{ route('admin.customers.reset', $customer->id) }}">
                    @csrf
                    <button class="px-3 py-2 rounded-lg border border-neutral-700 text-xs text-neutral-200">Réinitialiser mot de passe</button>
                </form>
                <form method="post" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Supprimer ce client ?');">
                    @csrf
                    @method('delete')
                    <button class="px-3 py-2 rounded-lg border border-red-500/60 text-xs text-red-300">Supprimer</button>
                </form>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <section class="rounded-2xl border border-neutral-800 bg-neutral-950 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-neutral-50">Commandes</h2>
                    <span class="text-xs text-neutral-400">{{ $orders->total() }} au total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-neutral-300">
                        <thead class="border-b border-neutral-800 text-neutral-500">
                        <tr>
                            <th class="py-2 text-left">Commande</th>
                            <th class="py-2 text-left">Date</th>
                            <th class="py-2 text-left">Statut</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr class="border-b border-neutral-900 last:border-0">
                                <td class="py-2 text-neutral-100">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-2 text-neutral-400">{{ $order->created_at?->format('d/m/Y') }}</td>
                                <td class="py-2">
                                    <span class="rounded-full bg-neutral-800 px-2 py-1 text-[10px] uppercase tracking-wide">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-2 text-right text-neutral-50">{{ number_format($order->total,2,',',' ') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-neutral-500">Aucune commande pour ce client.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </section>

            <section class="rounded-2xl border border-neutral-800 bg-neutral-950 p-4">
                <h2 class="text-sm font-semibold text-neutral-50 mb-3">Adresses</h2>
                <ul class="space-y-3 text-xs text-neutral-300">
                    @forelse($addresses as $address)
                        <li class="rounded-xl border border-neutral-800 bg-neutral-900 px-3 py-3">
                            <p class="text-[10px] uppercase tracking-wide text-neutral-500">{{ strtoupper($address->type ?? 'livraison') }}</p>
                            <p class="text-neutral-100">{{ $address->name ?? $customer->name }}</p>
                            <p class="text-neutral-400">{{ $address->line1 }}</p>
                            <p class="text-neutral-400">{{ $address->postal_code }} {{ $address->city }}, {{ $address->country }}</p>
                            @if($address->phone)
                                <p class="text-neutral-500 mt-1">{{ $address->phone }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-neutral-500">Aucune adresse enregistrée.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </section>
@endsection














