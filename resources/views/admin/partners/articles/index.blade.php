@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Articles – {{ $partner->name }}</h1>
        <div class="flex items-center gap-3">
            <form method="get" action="{{ route('admin.partners.articles.index', $partner->id) }}" class="flex items-center gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="Recherche titre" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Filtrer</button>
            </form>
            <a href="{{ route('admin.partners.articles.create', $partner->id) }}" class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Nouvel article</a>
        </div>
    </div>
    <div class="rounded-2xl border border-neutral-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-neutral-950 text-neutral-400">
                <tr>
                    <th class="px-3 py-2 text-left">Titre</th>
                    <th class="px-3 py-2 text-left">Publication</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $a)
                    <tr class="border-t border-neutral-800">
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                @if($a->cover_path)
                                    <img src="{{ $a->cover_path }}" class="h-8 w-8 rounded object-cover" />
                                @endif
                                <div class="font-semibold text-neutral-100">{{ $a->title }}</div>
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ optional($a->published_at)->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.partners.articles.edit', [$partner->id, $a->id]) }}" class="text-sky-400">Modifier</a>
                            <span class="mx-2 text-neutral-700">|</span>
                            <form action="{{ route('admin.partners.articles.destroy', [$partner->id, $a->id]) }}" method="post" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-rose-400" onclick="return confirm('Supprimer cet article ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-3 py-6 text-center text-neutral-500">Aucun article pour ce partenaire.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $articles->links() }}</div>
@endsection
