@extends('layouts.app')
@section('content')
    @php
        $subtotal = $summary['subtotal'] ?? 0;
        $taxRate = 0.18;
        $discount = $summary['discount'] ?? 0;
        $taxes = round($subtotal * $taxRate, 2);
        $estimatedTotal = $subtotal + $taxes - $discount;
    @endphp
    <section class="max-w-5xl mx-auto px-4 py-10 space-y-8">
        <div class="text-center">
            <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Étapes du checkout</p>
            <h1 class="text-3xl md:text-4xl font-semibold mt-2">Finalisez votre commande en toute sécurité</h1>
            <p class="mt-2 text-neutral-600">Identification → Adresse → Livraison → Paiement</p>
        </div>
        <form method="post" action="/checkout" class="grid gap-8 lg:grid-cols-[1.2fr,0.8fr]">
            @csrf
            <div class="space-y-6">
                <section class="rounded-3xl border border-neutral-100 bg-white p-6 space-y-4">
                    <h2 class="text-xl font-semibold">1. Informations personnelles</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="text-sm font-medium text-neutral-700">
                            Nom complet
                            <input name="name" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2" required>
                        </label>
                        <label class="text-sm font-medium text-neutral-700">
                            Téléphone
                            <input name="phone" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                        </label>
                    </div>
                </section>
                <section class="rounded-3xl border border-neutral-100 bg-white p-6 space-y-4">
                    <h2 class="text-xl font-semibold">2. Adresse</h2>
                    <label class="text-sm font-medium text-neutral-700">
                        Adresse (ligne 1)
                        <input name="line1" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2" required>
                    </label>
                    <label class="text-sm font-medium text-neutral-700">
                        Adresse (ligne 2)
                        <input name="line2" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                    </label>
                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="text-sm font-medium text-neutral-700">
                            Ville
                            <input name="city" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2" required>
                        </label>
                        <label class="text-sm font-medium text-neutral-700">
                            État / Région
                            <input name="state" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                        </label>
                        <label class="text-sm font-medium text-neutral-700">
                            Code postal
                            <input name="postal_code" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2" required>
                        </label>
                    </div>
                    <label class="text-sm font-medium text-neutral-700">
                        Pays
                        <input name="country" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2" required>
                    </label>
                </section>
                <section class="rounded-3xl border border-neutral-100 bg-white p-6 space-y-4">
                    <h2 class="text-xl font-semibold">3. Livraison</h2>
                    <label class="flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3">
                        <div>
                            <p class="font-semibold">Express 24h</p>
                            <p class="text-sm text-neutral-500">Chronopost / DHL – suivi temps réel</p>
                        </div>
                        <input type="radio" name="delivery_option" value="express" checked>
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3">
                        <div>
                            <p class="font-semibold">Standard 3-5 jours</p>
                            <p class="text-sm text-neutral-500">Transporteur partenaire</p>
                        </div>
                        <input type="radio" name="delivery_option" value="standard">
                    </label>
                    <label class="flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3">
                        <div>
                            <p class="font-semibold">Retrait en agence</p>
                            <p class="text-sm text-neutral-500">Gratuit</p>
                        </div>
                        <input type="radio" name="delivery_option" value="pickup">
                    </label>
                </section>
                <section class="rounded-3xl border border-neutral-100 bg-white p-6 space-y-4">
                    <h2 class="text-xl font-semibold">4. Paiement</h2>
                    <label class="flex items-center gap-3 rounded-2xl border border-neutral-200 px-4 py-3">
                        <input type="radio" name="payment_method" value="card" checked>
                        <div>
                            <p class="font-semibold">Carte bancaire (Stripe)</p>
                            <p class="text-sm text-neutral-500">Visa, Mastercard, AmEx</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-neutral-200 px-4 py-3">
                        <input type="radio" name="payment_method" value="paypal">
                        <div>
                            <p class="font-semibold">PayPal</p>
                            <p class="text-sm text-neutral-500">Paiement en 4x possible</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-neutral-200 px-4 py-3">
                        <input type="radio" name="payment_method" value="wire">
                        <div>
                            <p class="font-semibold">Virement / Paiement à la livraison</p>
                            <p class="text-sm text-neutral-500">Validation sous 24h</p>
                        </div>
                    </label>
                </section>
            </div>
            <aside class="rounded-3xl border border-neutral-100 bg-white p-6 space-y-6 h-fit">
                <div>
                    <h3 class="text-lg font-semibold">Résumé de la commande</h3>
                    <p class="text-sm text-neutral-500 mt-1">Taxes et frais calculés automatiquement</p>
                </div>
                <dl class="space-y-3 text-sm text-neutral-600">
                    <div class="flex items-center justify-between">
                        <dt>Panier</dt>
                        <dd>{{ number_format($subtotal, 2, ',', ' ') }} €</dd>
                    </div>
                    @if($discount > 0)
                    <div class="flex items-center justify-between text-emerald-700">
                        <dt>Code promo {{ $summary['coupon'] ? '(' . $summary['coupon'] . ')' : '' }}</dt>
                        <dd>-{{ number_format($discount, 2, ',', ' ') }} €</dd>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <dt>Livraison estimée</dt>
                        <dd>À partir de 0 €</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>TVA (18%)</dt>
                        <dd>{{ number_format($taxes, 2, ',', ' ') }} €</dd>
                    </div>
                </dl>
                <div class="flex items-center justify-between text-lg font-semibold text-neutral-900">
                    <span>Total estimé</span>
                    <span>{{ number_format($estimatedTotal, 2, ',', ' ') }} €</span>
                </div>
                <button class="w-full rounded-2xl bg-sky-600 py-3 text-white font-semibold hover:bg-sky-700">Confirmer la commande</button>
                <p class="text-xs text-neutral-500 text-center">En confirmant, vous acceptez nos CGV et la politique de confidentialité.</p>
            </aside>
        </form>
    </section>
@endsection
