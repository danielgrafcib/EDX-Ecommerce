@extends('layouts.admin')
@section('content')
    <h1 class="text-2xl font-semibold mb-6">Nouvel article – {{ $partner->name }}</h1>
    <form method="post" action="{{ route('admin.partners.articles.store', $partner->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm text-neutral-400">Titre</label>
            <input name="title" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm text-neutral-400">Contenu</label>
            <textarea name="content" rows="6" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2"></textarea>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm text-neutral-400">Date de publication</label>
                <input name="published_at" type="date" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
            <div class="flex items-center gap-4">
                <label class="text-sm text-neutral-400">Image de couverture</label>
                <input type="file" name="cover" accept="image/*" class="text-sm">
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm text-neutral-400">Catégories (séparées par des virgules)</label>
                <input name="categories" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" placeholder="Actualités, Lancements">
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Tags (séparés par des virgules)</label>
                <input name="tags" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" placeholder="tech, maison">
            </div>
        </div>
        <div>
            <button class="rounded-xl bg-neutral-200 text-neutral-900 px-4 py-2 text-sm font-semibold">Créer</button>
            <a href="{{ route('admin.partners.articles.index', $partner->id) }}" class="ml-2 text-sm text-neutral-400">Annuler</a>
        </div>
    </form>
@endsection
