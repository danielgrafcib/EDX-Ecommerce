@extends('layouts.app')
@section('content')
    @php
        $gallery = $product->images->pluck('path') ?? collect();
        $specs = [
            'Garantie' => '2 ans constructeur',
            'Livraison' => 'Express 24/48h assurée',
            'Retour' => '30 jours satisfait ou remboursé',
            'SKU' => $product->slug,
        ];
        $reviews = [
            ['author' => 'Nadia', 'rating' => 5, 'title' => 'Qualité premium', 'content' => 'Conforme à la description, finition parfaite.'],
            ['author' => 'Julien', 'rating' => 4, 'title' => 'Très satisfait', 'content' => 'Livraison rapide, communication claire.'],
        ];
    @endphp
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="grid gap-10 lg:grid-cols-2">
            <div>
                <div class="rounded-3xl bg-white border border-neutral-100 p-4">
                    @php($primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                    @if($primary)
                        <img src="{{ $primary->path }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full rounded-2xl object-cover">
                    @else
                        <div class="aspect-[4/3] rounded-2xl bg-neutral-100 grid place-items-center text-6xl">🖼️</div>
                    @endif
                </div>
                <div class="mt-4 flex gap-3 overflow-x-auto">
                    @forelse($gallery as $image)
                        <div class="h-20 w-20 rounded-2xl border border-neutral-200 bg-neutral-50 overflow-hidden">
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        </div>
                    @empty
                        @for($i=0;$i<4;$i++)
                            <div class="h-20 w-20 rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 grid place-items-center text-2xl">📸</div>
                        @endfor
                    @endforelse
                </div>
            </div>
            <div>
                <div class="flex items-center gap-3 text-sm text-neutral-600">
                    <span class="rounded-full bg-neutral-100 px-3 py-1">{{ optional($product->category)->name ?? 'Catégorie' }}</span>
                    <span>{{ $product->stock > 0 ? '✔ En stock' : '✖ Rupture temporaire' }}</span>
                    <span>Réf: {{ strtoupper($product->slug) }}</span>
                </div>
                <h1 class="mt-4 text-3xl md:text-4xl font-semibold text-neutral-900">{{ $product->name }}</h1>
                <p class="mt-2 text-sm text-neutral-500">Livraison gratuite au-delà de 500 € · Paiement sécurisé</p>
                <div class="mt-6 flex items-baseline gap-3">
                    <p class="text-4xl font-semibold text-neutral-900">{{ number_format($product->price, 2, ',', ' ') }} €</p>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">TVA incluse</span>
                </div>
                <p class="mt-6 text-neutral-700 leading-relaxed">{{ $product->description ?? 'Description à venir prochainement.' }}</p>

                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-medium text-neutral-700">
                        Variante
                        <select class="mt-2 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                            <option>Standard</option>
                            <option>Premium</option>
                            <option>Edition limitée</option>
                        </select>
                    </label>
                    <label class="text-sm font-medium text-neutral-700">
                        Couleur / Finition
                        <select class="mt-2 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                            <option>Noir</option>
                            <option>Argent</option>
                            <option>Blanc</option>
                        </select>
                    </label>
                </div>

                <form action="/cart" method="post" class="mt-8 flex flex-wrap gap-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <label class="text-sm font-medium text-neutral-700">
                        Quantité
                        <input type="number" name="quantity" value="1" min="1" class="mt-2 w-32 rounded-2xl border border-neutral-200 px-3 py-2">
                    </label>
                    <button class="flex-1 rounded-2xl bg-neutral-900 px-6 py-3 text-white font-semibold text-center">Ajouter au panier</button>
                    @auth
                        @if(!($inWishlist ?? false))
                        <form action="/wishlist" method="post">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button class="rounded-2xl border border-neutral-200 px-6 py-3 text-sm font-semibold text-neutral-700">Ajouter à la wishlist</button>
                        </form>
                        @else
                        <form action="/wishlist/{{ $product->id }}" method="post">
                            @csrf
                            @method('delete')
                            <button class="rounded-2xl border border-neutral-200 px-6 py-3 text-sm font-semibold text-neutral-700">Retirer de la wishlist</button>
                        </form>
                        @endif
                    @else
                        <a href="/login" class="rounded-2xl border border-neutral-200 px-6 py-3 text-sm font-semibold text-neutral-700">Ajouter à la wishlist</a>
                    @endauth
                </form>

                <dl class="mt-10 grid gap-4 sm:grid-cols-2">
                    @foreach($specs as $label => $value)
                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 px-4 py-3">
                            <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ $label }}</dt>
                            <dd class="text-sm font-semibold text-neutral-900 mt-1">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-[1.4fr,0.8fr]">
            <section class="rounded-3xl border border-neutral-100 bg-white p-6">
                <h2 class="text-2xl font-semibold mb-4">Détails & spécifications</h2>
                <p class="text-neutral-600 leading-relaxed">Chaque produit est vérifié par nos équipes techniques. Nous garantissons l’authenticité, la conformité CE et les meilleures performances selon les benchmarks 2025.</p>
                <ul class="mt-6 grid gap-2 text-neutral-700">
                    <li class="flex items-start gap-2"><span class="text-sky-600">•</span> Livraison sécurisée avec suivi GPS et assurance.</li>
                    <li class="flex items-start gap-2"><span class="text-sky-600">•</span> Service d’installation disponible pour les lits et véhicules.</li>
                    <li class="flex items-start gap-2"><span class="text-sky-600">•</span> Assistance produit par chat, téléphone et WhatsApp.</li>
                </ul>
            </section>
            <section class="rounded-3xl border border-neutral-100 bg-white p-6">
                <h3 class="text-xl font-semibold mb-4">Livraison & paiement</h3>
                <ul class="space-y-3 text-sm text-neutral-700">
                    <li><strong>Étape 1.</strong> Identification via compte client sécurisé.</li>
                    <li><strong>Étape 2.</strong> Choix adresse (livraison/facturation).</li>
                    <li><strong>Étape 3.</strong> Sélection du transporteur (Chrono, DHL, COD).</li>
                    <li><strong>Étape 4.</strong> Paiement Stripe, PayPal, virement ou à la livraison.</li>
                </ul>
            </section>
        </div>

        <section class="mt-12">
            <h2 class="text-2xl font-semibold mb-6">Avis clients</h2>
            <div class="grid gap-4 md:grid-cols-2">
                @foreach($reviews as $review)
                    <article class="rounded-3xl border border-neutral-100 bg-white p-5">
                        <div class="flex items-center justify-between text-sm text-neutral-500">
                            <span>{{ $review['author'] }}</span>
                            <span>{{ str_repeat('★', $review['rating']) }}</span>
                        </div>
                        <h3 class="mt-2 text-lg font-semibold text-neutral-900">{{ $review['title'] }}</h3>
                        <p class="mt-2 text-neutral-700">{{ $review['content'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="mt-12">
            <h2 class="text-xl font-semibold mb-4">Produits similaires</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($related as $p)
                    @php($rimg = $p->images->firstWhere('is_primary', true) ?? $p->images->first())
                    <a href="/product/{{ $p->id }}" class="group rounded-3xl border border-neutral-100 bg-white overflow-hidden hover:shadow-lg transition">
                        @if($rimg)
                            <img src="{{ $rimg->path }}" alt="{{ $p->name }}" class="h-44 w-full object-cover">
                        @else
                            <div class="h-44 bg-neutral-100 grid place-items-center">📦</div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-neutral-900 group-hover:text-sky-600">{{ $p->name }}</h3>
                            <p class="mt-1 text-neutral-600">{{ number_format($p->price, 2, ',', ' ') }} €</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
