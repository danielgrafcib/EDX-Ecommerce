@extends('layouts.admin')

@section('content')
    <section class="max-w-2xl mx-auto space-y-8">
        <header>
            <a href="{{ route('admin.markets.index') }}" class="text-sm text-neutral-400 hover:text-white mb-2 inline-block">&larr; Retour</a>
            <h1 class="text-2xl font-bold text-neutral-100">Nouveau Marché</h1>
        </header>

        <form action="{{ route('admin.markets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="space-y-4 rounded-xl border border-neutral-800 bg-neutral-900 p-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-300 mb-1">Nom du marché</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-neutral-300 mb-1">Slug (URL)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('slug') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-neutral-300 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">{{ old('description') }}</textarea>
                    @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-neutral-300 mb-1">Désignation Lieu (ex: Lomé, Digital)</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}"
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('location') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                 <!-- Image -->
                 <div>
                    <label for="image" class="block text-sm font-medium text-neutral-300 mb-1">Image (optionnel)</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full rounded-lg bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm text-neutral-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
                    @error('image') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Active -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                           class="rounded bg-neutral-950 border-neutral-800 text-sky-600 focus:ring-offset-neutral-900">
                    <label for="is_active" class="text-sm text-neutral-300">Marché actif</label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit" class="px-6 py-2 rounded-lg bg-sky-600 text-sm font-semibold text-white hover:bg-sky-500 transition">
                    Créer le marché
                </button>
            </div>
        </form>
    </section>
@endsection
