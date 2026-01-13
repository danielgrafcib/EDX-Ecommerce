@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Produits</h1>
                <p class="text-sm text-neutral-400">Gérez le catalogue : ajout, modification, activation, stock.</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Nouveau produit</a>
        </div>

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-2xl border border-neutral-800 bg-neutral-950">
            <table class="min-w-full text-xs text-neutral-300">
                <thead class="border-b border-neutral-800 text-neutral-500">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Catégorie</th>
                    <th class="p-3 text-left">Prix</th>
                    <th class="p-3 text-left">Stock</th>
                    <th class="p-3 text-left">Statut</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $p)
                    <tr class="border-b border-neutral-900 last:border-0">
                        <td class="p-3 text-neutral-500">#{{ $p->id }}</td>
                        <td class="p-3 text-neutral-100">{{ $p->name }}</td>
                        <td class="p-3 text-neutral-400">{{ optional($p->category)->name ?? '—' }}</td>
                        <td class="p-3">{{ number_format($p->price,2,',',' ') }} €</td>
                        <td class="p-3">{{ $p->stock }}</td>
                        <td class="p-3">
                            <span class="rounded-full px-2 py-1 text-[10px] uppercase tracking-wide
                                {{ $p->is_active ? 'bg-emerald-500/10 text-emerald-300' : 'bg-neutral-800 text-neutral-400' }}">
                                {{ $p->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.products.edit', $p->id) }}" class="px-3 py-1 rounded-lg border border-neutral-700 text-xs text-neutral-200">Éditer</a>
                            <form method="post" action="{{ route('admin.products.destroy', $p->id) }}" class="inline" onsubmit="return confirm('Supprimer ce produit ?');">
                                @csrf
                                @method('delete')
                                <button class="px-3 py-1 rounded-lg border border-red-500/60 text-xs text-red-300">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-neutral-500">Aucun produit pour le moment.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-neutral-900">
                {{ $products->links() }}
            </div>
        </div>
    </section>
@endsection
