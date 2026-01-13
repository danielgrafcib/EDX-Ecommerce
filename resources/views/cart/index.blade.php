@extends('layouts.app')
@section('content')
    @php
        $subtotal = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $taxRate = 0.18;
        $taxes = round($subtotal * $taxRate, 2);
        $shipping = $subtotal >= 500 ? 0 : 25;
        $discount = ($discount ?? 0);
        $grandTotal = $subtotal + $taxes + $shipping - $discount;
        $steps = [
            ['label' => 'Identification', 'status' => 'done'],
            ['label' => 'Adresse', 'status' => 'current'],
            ['label' => 'Livraison', 'status' => 'upcoming'],
            ['label' => 'Paiement', 'status' => 'upcoming'],
        ];
    @endphp
    <section class="max-w-7xl mx-auto px-4 py-10 space-y-8">
        <div class="rounded-3xl border border-neutral-100 bg-white p-6">
            <div class="flex items-center gap-4 overflow-x-auto">
                @foreach($steps as $index => $step)
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full grid place-items-center text-sm font-semibold
                                @if($step['status']==='done') bg-emerald-500 text-white
                                @elseif($step['status']==='current') bg-fuchsia-600 text-white
                                @else bg-neutral-100 text-neutral-500 @endif">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-neutral-500">{{ $step['label'] }}</p>
                                <p class="text-sm font-semibold text-neutral-900">{{ $step['status']==='current' ? 'En cours' : ($step['status']==='done' ? 'Validé' : 'À venir') }}</p>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <div class="h-px w-10 bg-neutral-200"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1.5fr,0.8fr]">
            <div class="space-y-4">
                @forelse($cart->items as $item)
                    <article class="rounded-3xl border border-neutral-100 bg-white p-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-20 w-20 rounded-2xl bg-neutral-100 grid place-items-center text-3xl">🛒</div>
                            <div>
                                <p class="text-sm uppercase tracking-wide text-neutral-500">{{ optional($item->product->category)->name ?? 'Catégorie' }}</p>
                                <h3 class="text-lg font-semibold text-neutral-900">{{ $item->product->name }}</h3>
                                <p class="text-sm text-neutral-500">{{ number_format($item->unit_price,2,',',' ') }} € · {{ $item->quantity }} pièces</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 md:items-end">
                            <form method="post" action="/cart/{{ $item->id }}" class="flex items-center gap-3">
                                @csrf
                                @method('put')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-20 rounded-2xl border border-neutral-200 px-3 py-2">
                                <button class="rounded-2xl border border-neutral-200 px-3 py-2 text-sm font-semibold">Mettre à jour</button>
                            </form>
                            <form method="post" action="/cart/{{ $item->id }}">
                                @csrf
                                @method('delete')
                                <button class="text-sm font-semibold text-red-600">Retirer</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-neutral-200 bg-neutral-50 p-10 text-center text-neutral-600">
                        Votre panier est vide. <a href="/catalog" class="text-sky-600 font-semibold">Découvrir le catalogue</a>
                    </div>
                @endforelse

                <div class="rounded-3xl border border-neutral-100 bg-white p-5">
                    <h3 class="text-lg font-semibold mb-4">Code promo</h3>
                    @if(!empty($appliedCoupon))
                        <div class="flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3">
                            <div class="text-sm">
                                <p class="font-semibold">{{ $appliedCoupon->code }}</p>
                                <p class="text-neutral-500">Réduction {{ $appliedCoupon->type==='percent' ? $appliedCoupon->value.'%' : number_format($appliedCoupon->value,2,',',' ').' €' }}</p>
                            </div>
                            <form action="/cart/coupon" method="post">
                                @csrf
                                @method('delete')
                                <button class="rounded-2xl border border-neutral-200 px-4 py-2 text-sm font-semibold">Retirer</button>
                            </form>
                        </div>
                    @else
                        <form action="/cart/coupon" method="post" class="flex flex-wrap gap-3">
                            @csrf
                            <input type="text" name="code" placeholder="EXCLU2025" class="flex-1 rounded-2xl border border-neutral-200 px-4 py-2" required>
                            <button type="submit" class="rounded-2xl bg-neutral-900 px-6 py-2 text-white font-semibold">Appliquer</button>
                        </form>
                    @endif
                </div>
            </div>

            <aside class="rounded-3xl border border-neutral-100 bg-white p-6 h-fit space-y-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">Récapitulatif</h2>
                    <dl class="space-y-3 text-sm text-neutral-600">
                        <div class="flex items-center justify-between">
                            <dt>Sous-total</dt>
                            <dd>{{ number_format($subtotal,2,',',' ') }} €</dd>
                        </div>
                        @if($discount > 0)
                        <div class="flex items-center justify-between text-emerald-700">
                            <dt>Code promo</dt>
                            <dd>-{{ number_format($discount,2,',',' ') }} €</dd>
                        </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <dt>Taxes ({{ $taxRate*100 }} %)</dt>
                            <dd>{{ number_format($taxes,2,',',' ') }} €</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Livraison</dt>
                            <dd>{{ $shipping === 0 ? 'Offerte' : number_format($shipping,2,',',' ').' €' }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex items-center justify-between text-lg font-semibold text-neutral-900">
                        <span>Total TTC</span>
                        <span>{{ number_format($grandTotal,2,',',' ') }} €</span>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-neutral-500 uppercase tracking-wide mb-3">Mode de livraison</h3>
                    <label class="flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3 text-sm">
                        <span>
                            <strong>Express 24h</strong>
                            <p class="text-neutral-500">Chronopost / DHL</p>
                        </span>
                        <input type="radio" name="shipping_method" checked>
                    </label>
                    <label class="mt-3 flex items-center justify-between rounded-2xl border border-neutral-200 px-4 py-3 text-sm">
                        <span>
                            <strong>Point relais</strong>
                            <p class="text-neutral-500">Mondial Relay</p>
                        </span>
                        <input type="radio" name="shipping_method">
                    </label>
                </div>

                <a href="/checkout" class="block text-center rounded-2xl bg-sky-600 py-3 font-semibold text-white hover:bg-sky-700">Passer à l’étape 2</a>
                <a href="/catalog" class="block text-center text-sm text-neutral-500 hover:text-neutral-900">Poursuivre mes achats</a>
            </aside>
        </div>
    </section>
@endsection
