@extends('layouts.app')
@section('content')
    @php
        $activeFilters = collect([
            'Recherche' => $filters['q'] ?? null,
            'Catégorie' => optional($categories->firstWhere('id', $filters['category_id'] ?? null))->name ?? null,
            'Prix min' => $filters['price_min'] ?? null,
            'Prix max' => $filters['price_max'] ?? null,
            'En stock' => request()->boolean('in_stock') ? 'Oui' : null,
        ])->filter();
    @endphp
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Catalogue complet</p>
                <h1 class="text-3xl md:text-4xl font-semibold mt-2 text-neutral-900">Parcourez {{ $catalogStats['total'] }} références triées par experts</h1>
                <p class="mt-2 text-neutral-600">Filtres dynamiques, tri intelligent, pagination optimisée.</p>
            </div>
            <div class="grid grid-cols-2 gap-4 lg:w-1/3">
                <div class="rounded-2xl border border-neutral-100 bg-white p-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">Produits en ligne</p>
                    <p class="mt-1 text-2xl font-semibold text-neutral-900">{{ $catalogStats['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-neutral-100 bg-white p-4 text-center">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">En stock</p>
                    <p class="mt-1 text-2xl font-semibold text-emerald-600">{{ $catalogStats['inStock'] }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[280px,1fr]">
            <aside class="rounded-3xl border border-neutral-100 bg-white p-6 h-fit">
                <h2 class="text-lg font-semibold mb-4">Filtres avancés</h2>
                <form method="get" class="space-y-5">
                    <div>
                        <label class="text-sm font-medium text-neutral-700">Recherche</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Lits, Tesla..." class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2 focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-neutral-700">Catégorie</label>
                        <select name="category_id" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                            <option value="">Toutes</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                                    {{ $category->name }} ({{ $category->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-neutral-700">Prix min (€)</label>
                            <input type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-neutral-700">Prix max (€)</label>
                            <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm font-medium text-neutral-700">
                        <input type="checkbox" name="in_stock" value="1" class="rounded border-neutral-300 text-sky-600 focus:ring-sky-500" @checked(request()->boolean('in_stock'))>
                        Disponible immédiatement
                    </label>
                    <div>
                        <label class="text-sm font-medium text-neutral-700">Tri</label>
                        <select name="sort" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                            <option value="new" @selected(($filters['sort'] ?? 'new') === 'new')>Nouveautés</option>
                            <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>Popularité</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Prix croissant</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Prix décroissant</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-neutral-700">Par page</label>
                        <select name="per_page" class="mt-1 w-full rounded-2xl border border-neutral-200 px-3 py-2">
                            @foreach([12,24,36,48] as $size)
                                <option value="{{ $size }}" @selected(request('per_page',12)==$size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="w-full rounded-2xl bg-sky-600 py-2.5 text-white font-semibold hover:bg-sky-700">Appliquer</button>
                    <a href="/catalog" class="block text-center text-sm text-neutral-500 hover:text-neutral-900">Réinitialiser</a>
                </form>
            </aside>
            <div>
                @if($activeFilters->isNotEmpty())
                    <div class="mb-5 flex flex-wrap gap-3">
                        @foreach($activeFilters as $label => $value)
                            <span class="inline-flex items-center gap-2 rounded-full border border-neutral-200 px-3 py-1 text-sm text-neutral-600">
                                {{ $label }} : <strong>{{ $value }}</strong>
                            </span>
                        @endforeach
                    </div>
                @endif
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse($products as $product)
                        <article class="rounded-3xl border border-neutral-100 bg-white p-5 shadow-sm hover:shadow-lg transition flex flex-col gap-4">
                            <div class="relative">
                                @php($primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                                @if($primary)
                                    <img src="{{ $primary->path }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full rounded-2xl object-cover" />
                                @else
                                    <div class="aspect-[4/3] rounded-2xl bg-neutral-100 grid place-items-center text-5xl">🛍️</div>
                                @endif
                                <span class="absolute top-3 right-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold {{ $product->stock > 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $product->stock > 0 ? 'En stock' : 'Rupture' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs uppercase tracking-wide text-neutral-500">{{ optional($product->category)->name ?? 'Catégorie' }}</p>
                                <h3 class="text-xl font-semibold text-neutral-900">{{ $product->name }}</h3>
                                <p class="text-neutral-600">{{ $product->description }}</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-lg font-semibold text-neutral-900">{{ number_format($product->price, 2, ',', ' ') }} €</p>
                                    <p class="text-xs text-neutral-500">Livraison 24/48h</p>
                                </div>
                                <div class="flex gap-2">
                                    <form action="/cart" method="post">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="rounded-full bg-neutral-900 text-white px-4 py-2 text-sm font-semibold">Ajouter</button>
                                    </form>
                                    <a href="/product/{{ $product->id }}" class="rounded-full border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 hover:border-sky-500">Voir</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-3xl border border-dashed border-neutral-200 bg-neutral-50 p-10 text-center text-neutral-600">
                            Aucun produit ne correspond aux filtres sélectionnés.
                        </div>
                    @endforelse
                </div>
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
