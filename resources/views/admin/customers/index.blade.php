@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Clients</h1>
                <p class="text-sm text-neutral-400">Liste des comptes clients, avec recherche par nom ou email.</p>
            </div>
            <form method="get" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Rechercher un client..." class="w-48 rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-xs text-neutral-50">
                <button class="px-3 py-2 rounded-lg bg-neutral-800 text-xs text-neutral-100">Filtrer</button>
            </form>
        </header>

        <div class="overflow-x-auto rounded-2xl border border-neutral-800 bg-neutral-950">
            <table class="min-w-full text-xs text-neutral-300">
                <thead class="border-b border-neutral-800 text-neutral-500">
                <tr>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Créé le</th>
                    <th class="p-3 text-right">Commandes</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr class="border-b border-neutral-900 last:border-0">
                        <td class="p-3 text-neutral-100">{{ $customer->name }}</td>
                        <td class="p-3 text-neutral-400">{{ $customer->email }}</td>
                        <td class="p-3 text-neutral-400">{{ $customer->created_at?->format('d/m/Y') }}</td>
                        <td class="p-3 text-right">{{ $customer->orders()->count() }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="px-3 py-1 rounded-lg border border-neutral-700 text-[11px] text-neutral-200">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-neutral-500">Aucun client trouvé.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-neutral-900">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
    </section>
@endsection






















