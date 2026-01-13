@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Modifier le produit</h1>
                <p class="text-sm text-neutral-400">Mettez à jour les informations du produit et son statut.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200 underline">Retour à la liste</a>
        </header>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.products.update', $product->id) }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-xs font-medium text-neutral-300">
                    Nom
                    <input name="name" value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Slug
                    <input name="slug" value="{{ old('slug', $product->slug) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <label class="text-xs font-medium text-neutral-300">
                    Catégorie
                    <select name="category_id" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                        <option value="">— Aucune —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Partenaire
                    <select name="partner_id" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                        <option value="">— Aucun —</option>
                        @foreach($partners as $p)
                            <option value="{{ $p->id }}" @selected(old('partner_id', $product->partner_id) == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Prix (€)
                    <input name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Stock
                    <input name="stock" type="number" value="{{ old('stock', $product->stock) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
            </div>
            <label class="text-xs font-medium text-neutral-300 block">
                Description
                <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">{{ old('description', $product->description) }}</textarea>
            </label>
            <div class="grid gap-3">
                <h3 class="text-xs font-semibold text-neutral-300">Images du produit</h3>
                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach($product->images as $img)
                        <div class="rounded-xl border border-neutral-800 bg-neutral-950 p-3">
                            <img src="{{ $img->path }}" alt="" class="aspect-[4/3] w-full rounded-lg object-cover">
                            <div class="mt-2 flex items-center justify-between text-xs text-neutral-400">
                                <div class="flex items-center gap-2">
                                    <span>{{ $img->is_primary ? 'Principale' : 'Secondaire' }}</span>
                                    @unless($img->is_primary)
                                        <form method="post" action="{{ route('admin.products.images.primary', [$product->id, $img->id]) }}">
                                            @csrf
                                            @method('put')
                                            <button class="px-2 py-1 rounded-lg border border-neutral-700 text-neutral-200">Définir comme principale</button>
                                        </form>
                                    @endunless
                                </div>
                                <form method="post" action="{{ route('admin.products.images.destroy', [$product->id, $img->id]) }}" onsubmit="return confirm('Supprimer cette image ?');">
                                    @csrf
                                    @method('delete')
                                    <button class="px-2 py-1 rounded-lg border border-red-500/50 text-red-300">Supprimer</button>
                                </form>
                            </div>
                            <form method="post" action="{{ route('admin.products.images.update', [$product->id, $img->id]) }}" enctype="multipart/form-data" class="mt-2 flex items-center gap-2">
                                @csrf
                                @method('put')
                                <input type="file" name="image" accept="image/*" class="flex-1 rounded-lg border border-neutral-700 bg-neutral-950 px-2 py-1 text-xs text-neutral-50" required>
                                <button class="px-2 py-1 rounded-lg bg-sky-600 text-xs font-semibold text-white">Remplacer</button>
                            </form>
                        </div>
                    @endforeach
                </div>
                <label class="text-xs font-medium text-neutral-300 block">
                    Ajouter des images
                    <input type="file" name="images[]" multiple accept="image/*" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
            </div>
            <label class="inline-flex items-center gap-2 text-xs font-medium text-neutral-300">
                <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-600 bg-neutral-950 text-sky-500" @checked(old('is_active', $product->is_active))>
                Produit actif
            </label>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Enregistrer</button>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Annuler</a>
            </div>
        </form>
    </section>
@endsection












