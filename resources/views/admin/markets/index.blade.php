@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Marchés</h1>
                <p class="text-sm text-neutral-400">Gérez les marchés (Lomé, numérique, etc.) et leur statut.</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="get" action="{{ route('admin.markets.index') }}" class="flex flex-wrap items-center gap-2">
                    <input name="q" value="{{ request('q') }}" placeholder="Recherche nom / lieu" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                    <select name="active" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                        <option value="">Tous</option>
                        <option value="1" @selected(request('active')==='1')>Actifs</option>
                        <option value="0" @selected(request('active')==='0')>Inactifs</option>
                    </select>
                    <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Filtrer</button>
                </form>
                <a href="{{ route('admin.markets.create') }}" class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Nouveau marché</a>
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
                    <th class="px-3 py-2 text-left">Slug</th>
                    <th class="px-3 py-2 text-left">Lieu</th>
                    <th class="px-3 py-2 text-left">Actif</th>
                    <th class="px-3 py-2 text-left">Image</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($markets as $market)
                    <tr class="border-t border-neutral-800">
                        <td class="px-3 py-2">{{ $market->name }}</td>
                        <td class="px-3 py-2 text-xs text-neutral-500">{{ $market->slug }}</td>
                        <td class="px-3 py-2">{{ $market->location ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $market->is_active ? 'Oui' : 'Non' }}</td>
                        <td class="px-3 py-2">
                            @if($market->image_path)
                                <img src="{{ $market->image_path }}" alt="" class="h-10 w-16 rounded object-cover">
                            @else
                                <span class="text-neutral-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.markets.edit', $market->id) }}" class="text-sky-400">Modifier</a>
                            <span class="mx-2 text-neutral-700">|</span>
                            <form action="{{ route('admin.markets.destroy', $market->id) }}" method="post" class="inline" onsubmit="return confirm('Supprimer ce marché ?');">
                                @csrf
                                @method('delete')
                                <button class="text-xs px-2 py-1 rounded border border-red-600/40 text-red-300">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-neutral-500">Aucun marché trouvé</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $markets->links() }}</div>
    </section>
@endsection

