@extends('layouts.admin')

@section('content')
    <section class="max-w-2xl mx-auto space-y-8">
        <header class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.markets.index') }}" class="text-sm text-neutral-400 hover:text-white mb-2 inline-block">&larr; Retour</a>
                <h1 class="text-2xl font-bold text-neutral-100">Modifier Marché</h1>
            </div>
            <form action="{{ route('admin.markets.destroy', $market->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-500 hover:text-red-400">Supprimer</button>
            </form>
        </header>

        <form action="{{ route('admin.markets.update', $market->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4 rounded-xl border border-neutral-800 bg-neutral-900 p-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-300 mb-1">Nom du marché</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $market->name) }}" required
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-neutral-300 mb-1">Slug (URL)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $market->slug) }}" required
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('slug') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-neutral-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">{{ old('description', $market->description) }}</textarea>
                    @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-neutral-300 mb-1">Désignation Lieu (ex: Lomé, Digital)</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $market->location) }}"
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('location') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                 <!-- Image -->
                 <div class="space-y-2">
                    <label for="image" class="block text-sm font-medium text-neutral-300 mb-1">Image (optionnel)</label>
                    @if($market->image_path)
                        <img src="{{ $market->image_path }}" alt="" class="h-20 w-auto rounded-lg object-cover mb-2">
                    @endif
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('image') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Active -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $market->is_active) ? 'checked' : '' }}
                           class="rounded bg-neutral-950 border-neutral-800 text-sky-600 focus:ring-offset-neutral-900">
                    <label for="is_active" class="text-sm text-neutral-300">Marché actif</label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 rounded-lg bg-sky-600 text-sm font-semibold text-white hover:bg-sky-500 transition">
                    Mettre à jour
                </button>
            </div>
        </form>
    </section>
@endsection
