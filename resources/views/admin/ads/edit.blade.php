@extends('layouts.admin')
@section('content')
    <h1 class="text-2xl font-semibold mb-6">Modifier la publicité</h1>
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
        <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
    @endif
    <form id="main-form" method="post" action="{{ route('admin.ads.update', $ad->id) }}" enctype="multipart/form-data">
        @csrf
        @method('put')
    </form>

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm text-neutral-400 block">Titre
                <input form="main-form" name="title" value="{{ old('title', $ad->title) }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
            </label>
            <label class="text-sm text-neutral-400 block">Type
                <select form="main-form" name="media_type" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                    <option value="image" @selected(old('media_type', $ad->media_type)==='image')>Image</option>
                    <option value="video" @selected(old('media_type', $ad->media_type)==='video')>Vidéo</option>
                </select>
            </label>
        </div>
        <label class="text-sm text-neutral-400 block">Description (optionnelle)
            <textarea form="main-form" name="description" rows="3" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm" placeholder="Texte qui apparaîtra à côté de l'image sur la page d'accueil">{{ old('description', $ad->description) }}</textarea>
        </label>
        <div class="grid gap-4 md:grid-cols-2">
            <div class="text-sm text-neutral-400 block">
                <div class="mb-2">Média actuel</div>
                @php
                    $path = trim((string)$ad->media_path);
                    $isExternal = \Illuminate\Support\Str::startsWith($path, ['http://','https://']);
                    $mediaUrl = null;
                    if ($path) {
                        $ver = optional($ad->updated_at)->getTimestamp() ?? time();
                        $mediaUrl = $isExternal ? $path : url('/promo/media/'.$ad->id).'?v='.$ver;
                    }
                @endphp
                <div class="mt-2 rounded-xl border border-neutral-800 p-3">
                    @if($mediaUrl)
                        @if($ad->media_type==='image')
                            <img src="{{ $mediaUrl }}" class="w-full rounded object-cover">
                        @else
                            <video src="{{ $mediaUrl }}" controls class="w-full rounded"></video>
                        @endif
                    @else
                        <div class="text-neutral-600">Aucun média trouvé</div>
                    @endif
                </div>
            </div>
            <div class="text-sm text-neutral-400">
                Remplacer le média
                <form method="post" action="{{ route('admin.ads.media.update', $ad->id) }}" enctype="multipart/form-data" class="mt-2 space-y-2">
                    @csrf
                    @method('put')
                    <input type="file" name="media" accept="{{ $ad->media_type==='image' ? 'image/*' : 'video/*' }}" class="w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                    <button class="rounded-xl bg-sky-600 text-white px-4 py-2 text-sm font-semibold">Remplacer</button>
                </form>
            </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="text-sm text-neutral-400 block">Lien (optionnel)
                <input form="main-form" name="link_url" type="url" value="{{ old('link_url', $ad->link_url) }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </label>
            <label class="text-sm text-neutral-400 block">Ordre
                <input form="main-form" name="sort_order" type="number" value="{{ old('sort_order', $ad->sort_order) }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </label>
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input form="main-form" type="checkbox" name="is_active" value="1" class="rounded border-neutral-700" @checked(old('is_active', $ad->is_active))>
            Actif
        </label>
        <div>
            <button form="main-form" class="rounded-xl bg-neutral-200 text-neutral-900 px-4 py-2 text-sm font-semibold">Sauvegarder</button>
            <a href="{{ route('admin.ads.index') }}" class="ml-2 text-sm text-neutral-400">Retour</a>
        </div>
    </div>
@endsection
