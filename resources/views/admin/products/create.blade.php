@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Nouveau produit</h1>
                <p class="text-sm text-neutral-400">Ajoutez un article au catalogue en le liant à une catégorie.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200 underline">Retour à la liste</a>
        </header>

        <form method="post" action="{{ route('admin.products.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-xs font-medium text-neutral-300">
                    Nom
                    <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Slug
                    <input name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-4">
                <label class="text-xs font-medium text-neutral-300">
                    Catégorie
                    <select name="category_id" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                        <option value="">— Aucune —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Partenaire
                    <select name="partner_id" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                        <option value="">— Aucun —</option>
                        @foreach($partners as $p)
                            <option value="{{ $p->id }}" @selected(old('partner_id') == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Prix (€)
                    <input name="price" type="number" step="0.01" value="{{ old('price') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Stock
                    <input name="stock" type="number" value="{{ old('stock', 0) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
            </div>
            <label class="text-xs font-medium text-neutral-300 block">
                Description
                <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">{{ old('description') }}</textarea>
            </label>
            <label class="text-xs font-medium text-neutral-300 block">
                Images (JPEG/PNG)
                <input type="file" name="images[]" multiple accept="image/*" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                <span class="text-[11px] text-neutral-500">La première image sera utilisée comme principale.</span>
            </label>
            <label class="inline-flex items-center gap-2 text-xs font-medium text-neutral-300">
                <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-600 bg-neutral-950 text-sky-500" checked>
                Produit actif
            </label>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Enregistrer</button>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Annuler</a>
            </div>
        </form>
    </section>
@endsection












