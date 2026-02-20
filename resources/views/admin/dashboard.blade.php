@extends('layouts.admin')
@section('content')
    <section class="space-y-8">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-wide text-sky-400 font-semibold">Tableau de bord</p>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50 mt-1">Vue d’ensemble e‑commerce</h1>
                <p class="text-sm text-neutral-400 mt-1">Suivi temps réel des ventes, clients et stocks.</p>
            </div>
            <div class="flex gap-2">
                <a href="/" class="px-3 py-2 rounded-lg border border-neutral-700 text-xs text-neutral-300">Voir le site</a>
                <a href="/admin/products" class="px-3 py-2 rounded-lg bg-sky-600 text-xs font-semibold text-white">Nouveau produit</a>
            </div>
        </header>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Chiffre d’affaires total</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ number_format($totalRevenue,2,',',' ') }} €</p>
            </div>
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Commandes</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ $ordersCount }}</p>
            </div>
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Produits actifs</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ $productsCount }}</p>
            </div>
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Clients</p>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ $customersCount }}</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wide text-neutral-400">Entreprises</p>
                    <a href="/admin/enterprises" class="text-[11px] text-sky-400 hover:text-sky-300 underline">Gérer</a>
                </div>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ $enterprisesCount }}</p>
            </div>
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wide text-neutral-400">Publicités actives</p>
                    <a href="/admin/ads" class="text-[11px] text-sky-400 hover:text-sky-300 underline">Voir</a>
                </div>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ $activeAdsCount }}</p>
            </div>
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-wide text-neutral-400">Abonnements actifs</p>
                    <a href="/admin/enterprises" class="text-[11px] text-sky-400 hover:text-sky-300 underline">Attribuer plans</a>
                </div>
                <p class="mt-2 text-2xl font-semibold text-neutral-50">{{ $activeSubsCount }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.4fr,0.8fr]">
            <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-neutral-50">Commandes récentes</h2>
                    <a href="#" class="text-xs text-neutral-400 hover:text-neutral-200">Tout voir</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-neutral-300">
                        <thead class="border-b border-neutral-800 text-neutral-500">
                        <tr>
                            <th class="py-2 text-left">Commande</th>
                            <th class="py-2 text-left">Client</th>
                            <th class="py-2 text-left">Statut</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentOrders as $order)
                            <tr class="border-b border-neutral-900 last:border-0">
                                <td class="py-2 text-neutral-100">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-2">{{ $order->user?->name ?? 'Invité' }}</td>
                                <td class="py-2">
                                    <span class="rounded-full bg-neutral-800 px-2 py-1 text-[10px] uppercase tracking-wide">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="py-2 text-right text-neutral-50">{{ number_format($order->total,2,',',' ') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-neutral-500">Aucune commande récente.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-neutral-50">Stocks faibles</h2>
                    <a href="/admin/products" class="text-xs text-neutral-400 hover:text-neutral-200">Gérer</a>
                </div>
                <ul class="space-y-2 text-xs text-neutral-300">
                    @forelse($lowStockProducts as $product)
                        <li class="flex items-center justify-between rounded-xl border border-neutral-800 px-3 py-2">
                            <div>
                                <p class="text-neutral-100">{{ $product->name }}</p>
                                <p class="text-neutral-500">{{ optional($product->category)->name ?? 'Sans catégorie' }}</p>
                            </div>
                            <span class="text-amber-400 font-semibold">{{ $product->stock }} en stock</span>
                        </li>
                    @empty
                        <li class="text-neutral-500">Aucun produit en rupture imminente.</li>
                    @endforelse
                </ul>
            </section>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-neutral-50">Top produits</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-neutral-300">
                        <thead class="border-b border-neutral-800 text-neutral-500">
                        <tr>
                            <th class="py-2 text-left">Produit</th>
                            <th class="py-2 text-right">Quantité</th>
                            <th class="py-2 text-right">Revenu</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($topProducts as $tp)
                            <tr class="border-b border-neutral-900 last:border-0">
                                <td class="py-2 text-neutral-100">{{ $tp->product?->name ?? ('#'.$tp->product_id) }}</td>
                                <td class="py-2 text-right">{{ $tp->qty }}</td>
                                <td class="py-2 text-right">{{ number_format($tp->revenue ?? 0,2,',',' ') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-neutral-500">Aucune donnée.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-neutral-50">Nouveaux clients</h2>
                </div>
                <ul class="space-y-2 text-xs text-neutral-300">
                    @forelse($newCustomers as $nc)
                        <li class="flex items-center justify-between rounded-xl border border-neutral-800 px-3 py-2">
                            <span class="text-neutral-100">{{ $nc->name }}</span>
                            <span class="text-neutral-500">{{ $nc->created_at?->format('d/m/Y') }}</span>
                        </li>
                    @empty
                        <li class="text-neutral-500">Aucun nouveau client.</li>
                    @endforelse
                </ul>
            </section>
        </div>

        <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-neutral-50">Entreprises & Sponsoring</h2>
                <div class="flex items-center gap-2">
                    <a href="/admin/enterprises" class="text-xs rounded-lg border border-neutral-700 px-2 py-1">Entreprises</a>
                    <a href="/admin/ads" class="text-xs rounded-lg border border-neutral-700 px-2 py-1">Publicités</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-neutral-300">
                    <thead class="border-b border-neutral-800 text-neutral-500">
                        <tr>
                            <th class="py-2 text-left">Plan</th>
                            <th class="py-2 text-left">Période</th>
                            <th class="py-2 text-right">Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adPlans as $plan)
                            <tr class="border-b border-neutral-900 last:border-0">
                                <td class="py-2 text-neutral-100">{{ $plan->name }}</td>
                                <td class="py-2">{{ $plan->billing_period }}</td>
                                <td class="py-2 text-right">{{ number_format($plan->price,2,',',' ') }} €</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-neutral-500">Aucun plan actif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
@endsection
