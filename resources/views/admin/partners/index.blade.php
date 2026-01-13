@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Partenariats</h1>
        <div class="flex items-center gap-3">
            <form method="get" action="{{ route('admin.partners.index') }}" class="flex items-center gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="Recherche nom ou localisation" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                <select name="active" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('active')==='1')>Actifs</option>
                    <option value="0" @selected(request('active')==='0')>Inactifs</option>
                </select>
                <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Filtrer</button>
            </form>
            <a href="{{ route('admin.partners.create') }}" class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Nouveau partenaire</a>
        </div>
    </div>
    <div class="rounded-2xl border border-neutral-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-neutral-950 text-neutral-400">
                <tr>
                    <th class="px-3 py-2 text-left">Nom</th>
                    <th class="px-3 py-2 text-left">Localisation</th>
                    <th class="px-3 py-2 text-left">Produits liés</th>
                    <th class="px-3 py-2 text-left">Articles</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $p)
                    <tr class="border-t border-neutral-800">
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                @if($p->logo_path)
                                    <img src="{{ $p->logo_path }}" alt="logo" class="h-6 w-6 rounded" />
                                @endif
                                <div>
                                    <div class="font-semibold text-neutral-100">{{ $p->name }}</div>
                                    <div class="text-xs text-neutral-500">/{{ $p->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ $p->location ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $p->products_count }}</td>
                        <td class="px-3 py-2">{{ $p->articles_count }}</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.partners.edit', $p->id) }}" class="text-sky-400">Modifier</a>
                            <span class="mx-2 text-neutral-700">|</span>
                            <a href="{{ route('admin.partners.articles.index', $p->id) }}" class="text-emerald-400">Articles</a>
                            <span class="mx-2 text-neutral-700">|</span>
                            <form action="{{ route('admin.partners.destroy', $p->id) }}" method="post" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-rose-400" onclick="return confirm('Supprimer ce partenaire ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-6 text-center text-neutral-500">Aucun partenaire pour l’instant.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $partners->links() }}</div>
@endsection
