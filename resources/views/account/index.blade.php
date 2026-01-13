@extends('layouts.app')
@section('content')
    <section class="max-w-6xl mx-auto px-4 py-10 space-y-8">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Espace client</p>
                <h1 class="text-3xl font-semibold text-neutral-900 mt-2">Tableau de bord personnel</h1>
                <p class="text-neutral-600">Gérez vos informations, vos adresses, vos commandes et votre wishlist.</p>
            </div>
            <div class="flex gap-3">
                <a href="/cart" class="px-5 py-2 rounded-full border border-neutral-200 text-sm font-semibold text-neutral-700">Voir mon panier</a>
                <a href="/checkout" class="px-5 py-2 rounded-full bg-neutral-900 text-white text-sm font-semibold">Finaliser une commande</a>
            </div>
        </div>

        @if(!$user)
            <div class="rounded-3xl border border-dashed border-neutral-200 bg-neutral-50 p-10 text-center">
                <p class="text-lg font-semibold text-neutral-900">Vous n’êtes pas connecté.</p>
                <p class="text-neutral-600 mt-2">Créez un compte ou connectez-vous pour suivre vos commandes, enregistrer vos adresses et gérer votre wishlist.</p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="/login" class="px-6 py-3 rounded-full bg-sky-600 text-white font-semibold">Connexion</a>
                    <a href="/register" class="px-6 py-3 rounded-full border border-neutral-200 font-semibold text-neutral-700">Créer un compte</a>
                </div>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
                <section class="rounded-3xl border border-neutral-100 bg-white p-6 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="h-16 w-16 rounded-2xl bg-neutral-900 text-white grid place-items-center text-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-wide text-neutral-500">Profil</p>
                            <h2 class="text-2xl font-semibold">{{ $user->name }}</h2>
                            <p class="text-neutral-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    <dl class="grid gap-4 sm:grid-cols-3 text-sm">
                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                            <dt class="text-neutral-500">Commandes</dt>
                            <dd class="text-2xl font-semibold text-neutral-900">{{ $orders->count() }}</dd>
                        </div>
                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                            <dt class="text-neutral-500">Adresses</dt>
                            <dd class="text-2xl font-semibold text-neutral-900">{{ $addresses->count() }}</dd>
                        </div>
                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                            <dt class="text-neutral-500">Wishlist</dt>
                            <dd class="text-2xl font-semibold text-neutral-900">{{ $wishlistCount }}</dd>
                        </div>
                    </dl>
                </section>
                <section class="rounded-3xl border border-neutral-100 bg-white p-6">
                    <h2 class="text-xl font-semibold mb-4">Adresses sauvegardées</h2>
                    @forelse($addresses as $address)
                        <article class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4 mb-3 last:mb-0">
                            <p class="text-sm uppercase tracking-wide text-neutral-500">{{ strtoupper($address->type ?? 'livraison') }}</p>
                            <p class="font-semibold text-neutral-900">{{ $address->name ?? $user->name }}</p>
                            <p class="text-neutral-600">{{ $address->line1 }}</p>
                            <p class="text-neutral-600">{{ $address->postal_code }} {{ $address->city }}, {{ $address->country }}</p>
                            <p class="text-sm text-neutral-500 mt-1">{{ $address->phone }}</p>
                        </article>
                    @empty
                        <p class="text-neutral-500">Aucune adresse enregistrée.</p>
                    @endforelse
                </section>
            </div>

            <section class="rounded-3xl border border-neutral-100 bg-white p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">Historique des commandes</h2>
                    <a href="/orders" class="text-sm font-semibold text-neutral-500 hover:text-neutral-900">Tout voir →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-neutral-500 uppercase tracking-wide border-b">
                            <tr>
                                <th class="py-2">Commande</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr class="border-b last:border-0">
                                <td class="py-3 font-semibold text-neutral-900">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-neutral-600">{{ $order->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-600">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td class="font-semibold text-neutral-900">{{ number_format($order->total, 2, ',', ' ') }} €
                                    @if($order->tracking_url || $order->tracking_code)
                                        <div class="mt-1 text-xs text-neutral-600">
                                            @if($order->tracking_carrier)
                                                <span>{{ $order->tracking_carrier }}:</span>
                                            @endif
                                            @if($order->tracking_code)
                                                <span class="font-mono">{{ $order->tracking_code }}</span>
                                            @endif
                                            @if($order->tracking_url)
                                                <a href="{{ $order->tracking_url }}" target="_blank" class="ml-2 underline text-sky-600">Suivre</a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-neutral-500">Aucune commande récente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </section>
@endsection













