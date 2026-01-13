@extends('layouts.admin')
@section('content')
    <h1 class="text-2xl font-semibold mb-6">Modifier partenaire</h1>
    <form method="post" action="{{ route('admin.partners.update', $partner->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('put')
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm text-neutral-400">Nom</label>
                <input name="name" value="{{ $partner->name }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Slug</label>
                <input name="slug" value="{{ $partner->slug }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Localisation</label>
                <input name="location" value="{{ $partner->location }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Site web</label>
                <input name="website" type="url" value="{{ $partner->website }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm text-neutral-400">Description</label>
            <textarea name="description" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" rows="4">{{ $partner->description }}</textarea>
        </div>
        <div class="flex items-center gap-4">
            <label class="text-sm text-neutral-400">Logo (optionnel)</label>
            <input type="file" name="logo" accept="image/*" class="text-sm">
            @if($partner->logo_path)
                <img src="{{ $partner->logo_path }}" alt="logo" class="h-8 w-8 rounded" />
            @endif
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" class="rounded border-neutral-700" @checked($partner->is_active)>
            Actif
        </label>
        <div>
            <button class="rounded-xl bg-neutral-200 text-neutral-900 px-4 py-2 text-sm font-semibold">Sauvegarder</button>
            <a href="{{ route('admin.partners.index') }}" class="ml-2 text-sm text-neutral-400">Retour</a>
        </div>
    </form>

    <hr class="my-8 border-neutral-800">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">Produits liés</h2>
        <a href="{{ route('admin.partners.articles.index', $partner->id) }}" class="text-sm text-emerald-400">Gérer les articles →</a>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($partner->products as $prod)
            <div class="rounded-xl border border-neutral-800 p-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    @if($prod->images->count())
                        @php($pi = $prod->images->firstWhere('is_primary', true) ?? $prod->images->first())
                        <img src="{{ $pi->path }}" alt="" class="h-14 w-14 rounded object-cover">
                    @else
                        <div class="h-14 w-14 rounded bg-neutral-800"></div>
                    @endif
                    <div>
                        <div class="font-semibold">{{ $prod->name }}</div>
                        <div class="text-xs text-neutral-500">Cat: {{ optional($prod->category)->name ?? '—' }}</div>
                        <div class="text-sm text-neutral-300">{{ number_format($prod->price, 2, ',', ' ') }} €</div>
                    </div>
                </div>
                <form method="post" action="{{ route('admin.partners.detach', [$partner->id, $prod->id]) }}">
                    @csrf
                    @method('delete')
                    <button class="text-rose-400 text-sm">Dissocier</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-neutral-500">Aucun produit associé.</p>
        @endforelse
    </div>

    <div class="mt-6">
        <h3 class="text-sm uppercase tracking-wide text-neutral-400">Associer un produit</h3>
        <form method="post" action="{{ route('admin.partners.attach', $partner->id) }}" class="mt-2 flex items-center gap-2">
            @csrf
            <select name="product_id" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm">
                @foreach($unlinkedProducts as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Associer</button>
        </form>
        <div class="mt-2">{{ $unlinkedProducts->links() }}</div>
    </div>

    <hr class="my-8 border-neutral-800">
    <h2 class="text-lg font-semibold mb-4">Galerie du partenaire</h2>
    <form method="post" action="{{ route('admin.partners.images.add', $partner->id) }}" enctype="multipart/form-data" class="flex items-center gap-3 mb-4">
        @csrf
        <input type="file" name="image" accept="image/*" class="text-sm">
        <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Ajouter</button>
    </form>
    <div class="grid gap-4 md:grid-cols-4">
        @foreach($partner->images as $img)
            <div class="rounded-xl border border-neutral-800 p-3">
                <img src="{{ $img->path }}" class="aspect-[4/3] w-full rounded object-cover">
                <div class="mt-2 flex items-center justify-between text-sm">
                    <form method="post" action="{{ route('admin.partners.images.delete', [$partner->id,$img->id]) }}">
                        @csrf
                        @method('delete')
                        <button class="text-rose-400">Supprimer</button>
                    </form>
                    <form method="post" action="{{ route('admin.partners.images.primary', [$partner->id,$img->id]) }}">
                        @csrf
                        <button class="text-sky-400">Principal</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
