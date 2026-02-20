@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Entreprises</h1>
                <p class="text-sm text-neutral-400">Gérez les entreprises partenaires et leurs fiches.</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="get" action="{{ route('admin.enterprises.index') }}" class="flex flex-wrap items-center gap-2">
                    <input name="q" value="{{ request('q') }}" placeholder="Recherche nom / lieu" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                    <select name="status" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                        <option value="">Tous statuts</option>
                        <option value="pending" @selected(request('status')==='pending')>En attente</option>
                        <option value="approved" @selected(request('status')==='approved')>Approuvées</option>
                        <option value="rejected" @selected(request('status')==='rejected')>Rejetées</option>
                    </select>
                    <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Filtrer</button>
                </form>
                <a href="{{ route('admin.enterprises.create') }}" class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Nouvelle entreprise</a>
            </div>
        </header>

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-neutral-800 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-neutral-950 text-neutral-400">
                <tr>
                    <th class="px-3 py-2 text-left">Nom</th>
                    <th class="px-3 py-2 text-left">Lieu</th>
                    <th class="px-3 py-2 text-left">Site web</th>
                    <th class="px-3 py-2 text-left">Statut</th>
                    <th class="px-3 py-2 text-left">Actif</th>
                    <th class="px-3 py-2 text-left">Logo</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($enterprises as $enterprise)
                    <tr class="border-t border-neutral-800">
                        <td class="px-3 py-2">{{ $enterprise->name }}</td>
                        <td class="px-3 py-2">{{ $enterprise->location ?? '—' }}</td>
                        <td class="px-3 py-2 text-xs">
                            @if($enterprise->website)
                                <a href="{{ $enterprise->website }}" target="_blank" class="text-sky-400 underline">Voir</a>
                            @else
                                <span class="text-neutral-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs">
                            @switch($enterprise->status)
                                @case('approved') <span class="text-emerald-400">Approuvée</span> @break
                                @case('rejected') <span class="text-red-400">Rejetée</span> @break
                                @default <span class="text-amber-300">En attente</span>
                            @endswitch
                        </td>
                        <td class="px-3 py-2">{{ $enterprise->is_active ? 'Oui' : 'Non' }}</td>
                        <td class="px-3 py-2">
                            @if($enterprise->logo_path)
                                <img src="{{ $enterprise->logo_path }}" alt="" class="h-8 w-8 rounded-full object-cover">
                            @else
                                <span class="text-neutral-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.enterprises.edit', $enterprise->id) }}" class="text-sky-400">Modifier</a>
                            <span class="mx-2 text-neutral-700">|</span>
                            <form action="{{ route('admin.enterprises.destroy', $enterprise->id) }}" method="post" class="inline" onsubmit="return confirm('Supprimer cette entreprise ?');">
                                @csrf
                                @method('delete')
                                <button class="text-xs px-2 py-1 rounded border border-red-600/40 text-red-300">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-neutral-500">Aucune entreprise trouvée</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $enterprises->links() }}</div>
    </section>
@endsection

