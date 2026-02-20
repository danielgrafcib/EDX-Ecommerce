@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Catégories</h1>
                <p class="text-sm text-neutral-400">Structurez votre catalogue par univers et sous‑catégories.</p>
            </div>
        </header>

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="rounded-2xl border border-neutral-800 bg-neutral-950 p-4">
                <h2 class="text-sm font-semibold text-neutral-50 mb-3">Liste des catégories</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-neutral-300">
                        <thead class="border-b border-neutral-800 text-neutral-500">
                        <tr>
                            <th class="py-2 text-left">Nom</th>
                            <th class="py-2 text-left">Slug</th>
                            <th class="py-2 text-left">Parent</th>
                            <th class="py-2 text-right">Produits</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $category)
                            <tr class="border-b border-neutral-900 last:border-0">
                                <td class="py-2 text-neutral-100">{{ $category->name }}</td>
                                <td class="py-2 text-neutral-400">{{ $category->slug }}</td>
                                <td class="py-2 text-neutral-400">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="py-2 text-right">{{ $category->products_count }}</td>
                                <td class="py-2 text-right space-x-2">
                                    <form method="post" action="{{ route('admin.categories.update', $category->id) }}" class="inline-flex items-center gap-1">
                                        @csrf
                                        @method('put')
                                        <input type="hidden" name="name" value="{{ $category->name }}">
                                        <input type="hidden" name="slug" value="{{ $category->slug }}">
                                        <input type="hidden" name="description" value="{{ $category->description }}">
                                        <input type="hidden" name="parent_id" value="{{ $category->parent_id }}">
                                        <button class="px-3 py-1 rounded-lg border border-neutral-700 text-[11px] text-neutral-200">Revalider</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.categories.destroy', $category->id) }}" class="inline" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                        @csrf
                                        @method('delete')
                                        <button class="px-3 py-1 rounded-lg border border-red-500/60 text-[11px] text-red-300">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-neutral-500">Aucune catégorie.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $categories->links() }}
                </div>
            </div>

            <div class="rounded-2xl border border-neutral-800 bg-neutral-950 p-4">
                <h2 class="text-sm font-semibold text-neutral-50 mb-3">Nouvelle catégorie</h2>
                <form method="post" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf
                    <label class="text-xs font-medium text-neutral-300 block">
                        Nom
                        <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                    </label>
                    <label class="text-xs font-medium text-neutral-300 block">
                        Slug
                        <input name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                    </label>
                    <label class="text-xs font-medium text-neutral-300 block">
                        Parent
                        <select name="parent_id" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                            <option value="">— Aucune —</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-medium text-neutral-300 block">
                        Description
                        <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">{{ old('description') }}</textarea>
                    </label>
                    <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Créer</button>
                </form>
            </div>
        </div>
    </section>
@endsection





















