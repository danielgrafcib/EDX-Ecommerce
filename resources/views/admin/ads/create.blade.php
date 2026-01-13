@extends('layouts.admin')
@section('content')
    <h1 class="text-2xl font-semibold mb-6">Nouvelle publicité</h1>
    @if ($errors->any())
        <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="post" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm text-neutral-400 block">Titre
                <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
            </label>
            <label class="text-sm text-neutral-400 block">Type
                <select name="media_type" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                    <option value="image" @selected(old('media_type')==='image')>Image</option>
                    <option value="video" @selected(old('media_type')==='video')>Vidéo</option>
                </select>
            </label>
        </div>
        <label class="text-sm text-neutral-400 block">Description (optionnelle)
            <textarea name="description" rows="3" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm" placeholder="Texte qui apparaîtra à côté de l'image sur la page d'accueil">{{ old('description') }}</textarea>
        </label>
        <label class="text-sm text-neutral-400 block">Média
            <input type="file" name="media" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
        </label>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm text-neutral-400 block">Lien (optionnel)
                <input name="link_url" type="url" value="{{ old('link_url') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </label>
            <label class="text-sm text-neutral-400 block">Ordre
                <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </label>
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-700" @checked(old('is_active', true))>
            Actif
        </label>
        <div>
            <button class="rounded-xl bg-neutral-200 text-neutral-900 px-4 py-2 text-sm font-semibold">Créer</button>
            <a href="{{ route('admin.ads.index') }}" class="ml-2 text-sm text-neutral-400">Annuler</a>
        </div>
    </form>
@endsection
