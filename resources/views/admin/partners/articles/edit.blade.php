@extends('layouts.admin')
@section('content')
    <h1 class="text-2xl font-semibold mb-6">Modifier article – {{ $partner->name }}</h1>
    <form method="post" action="{{ route('admin.partners.articles.update', [$partner->id, $article->id]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('put')
        <div>
            <label class="block text-sm text-neutral-400">Titre</label>
            <input name="title" value="{{ $article->title }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm text-neutral-400">Contenu</label>
            <textarea name="content" rows="6" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">{{ $article->content }}</textarea>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm text-neutral-400">Date de publication</label>
                <input name="published_at" type="date" value="{{ optional($article->published_at)->format('Y-m-d') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
            <div class="flex items-center gap-4">
                <label class="text-sm text-neutral-400">Image de couverture</label>
                <input type="file" name="cover" accept="image/*" class="text-sm">
                @if($article->cover_path)
                    <img src="{{ $article->cover_path }}" class="h-10 w-10 rounded object-cover" />
                @endif
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm text-neutral-400">Catégories (séparées par des virgules)</label>
                <input name="categories" value="{{ $article->categories->pluck('name')->implode(', ') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Tags (séparés par des virgules)</label>
                <input name="tags" value="{{ $article->tags->pluck('name')->implode(', ') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
        </div>
        <div>
            <button class="rounded-xl bg-neutral-200 text-neutral-900 px-4 py-2 text-sm font-semibold">Sauvegarder</button>
            <a href="{{ route('admin.partners.articles.index', $partner->id) }}" class="ml-2 text-sm text-neutral-400">Retour</a>
        </div>
    </form>

    <hr class="my-8 border-neutral-800">
    <h2 class="text-lg font-semibold mb-4">Galerie d’images</h2>
    <form method="post" action="{{ route('admin.partners.articles.images.add', [$partner->id,$article->id]) }}" enctype="multipart/form-data" class="flex items-center gap-3 mb-4">
        @csrf
        <input type="file" name="image" accept="image/*" class="text-sm">
        <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Ajouter</button>
    </form>
    <div class="grid gap-4 md:grid-cols-4">
        @foreach($article->images as $img)
            <div class="rounded-xl border border-neutral-800 p-3">
                <img src="{{ $img->path }}" class="aspect-[4/3] w-full rounded object-cover">
                <div class="mt-2 flex items-center justify-between text-sm">
                    <form method="post" action="{{ route('admin.partners.articles.images.delete', [$partner->id,$article->id,$img->id]) }}">
                        @csrf
                        @method('delete')
                        <button class="text-rose-400">Supprimer</button>
                    </form>
                    <form method="post" action="{{ route('admin.partners.articles.images.primary', [$partner->id,$article->id,$img->id]) }}">
                        @csrf
                        <button class="text-sky-400">Principal</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    @endsection
