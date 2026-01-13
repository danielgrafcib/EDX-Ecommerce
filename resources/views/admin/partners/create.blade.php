@extends('layouts.admin')
@section('content')
    <h1 class="text-2xl font-semibold mb-6">Nouveau partenaire</h1>
    <form method="post" action="{{ route('admin.partners.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm text-neutral-400">Nom</label>
                <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Slug</label>
                <input name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Localisation</label>
                <input name="location" value="{{ old('location') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-neutral-400">Site web</label>
                <input name="website" type="url" value="{{ old('website') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
            </div>
        </div>
        <div>
            <label class="block text-sm text-neutral-400">Description</label>
            <textarea name="description" rows="4" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div class="flex items-center gap-4">
            <label class="text-sm text-neutral-400">Logo (optionnel)</label>
            <input type="file" name="logo" accept="image/*" class="text-sm">
        </div>
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_active" class="rounded border-neutral-700" @checked(old('is_active', true))>
            Actif
        </label>
        <div>
            <button class="rounded-xl bg-neutral-200 text-neutral-900 px-4 py-2 text-sm font-semibold">Créer</button>
            <a href="{{ route('admin.partners.index') }}" class="ml-2 text-sm text-neutral-400">Annuler</a>
        </div>
    </form>
@endsection
