@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-50">Codes promo</h1>
                <p class="text-sm text-neutral-400">Créez, activez et gérez vos codes de réduction.</p>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[1fr,0.8fr]">
            <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <table class="w-full text-xs text-neutral-300">
                    <thead class="border-b border-neutral-800 text-neutral-500">
                    <tr>
                        <th class="py-2 text-left">Code</th>
                        <th class="py-2 text-left">Type</th>
                        <th class="py-2 text-right">Valeur</th>
                        <th class="py-2 text-left">Début</th>
                        <th class="py-2 text-left">Fin</th>
                        <th class="py-2 text-left">Statut</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($coupons as $c)
                        <tr class="border-b border-neutral-900 last:border-0">
                            <td class="py-2 text-neutral-100">{{ $c->code }}</td>
                            <td class="py-2">{{ $c->type }}</td>
                            <td class="py-2 text-right">{{ $c->type==='percent' ? $c->value.' %' : number_format($c->value,2,',',' ').' €' }}</td>
                            <td class="py-2">{{ $c->starts_at ? $c->starts_at->format('d/m/Y') : '—' }}</td>
                            <td class="py-2">{{ $c->ends_at ? $c->ends_at->format('d/m/Y') : '—' }}</td>
                            <td class="py-2">{{ $c->status }}</td>
                            <td class="py-2 text-right">
                                <form action="/admin/coupons/{{ $c->id }}" method="post" class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="type" value="{{ $c->type }}">
                                    <input type="hidden" name="value" value="{{ $c->value }}">
                                    <input type="hidden" name="starts_at" value="{{ $c->starts_at }}">
                                    <input type="hidden" name="ends_at" value="{{ $c->ends_at }}">
                                    <input type="hidden" name="usage_limit" value="{{ $c->usage_limit }}">
                                    <input type="hidden" name="status" value="{{ $c->status==='active' ? 'inactive' : 'active' }}">
                                    <button class="px-3 py-1 rounded-lg border border-neutral-700 text-xs text-neutral-300">{{ $c->status==='active' ? 'Désactiver' : 'Activer' }}</button>
                                </form>
                                <form action="/admin/coupons/{{ $c->id }}" method="post" class="inline-flex">
                                    @csrf
                                    @method('delete')
                                    <button class="px-3 py-1 rounded-lg border border-red-700 text-xs text-red-300">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 text-center text-neutral-500">Aucun code promo.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-3 text-right text-xs text-neutral-500">{{ $coupons->links() }}</div>
            </section>

            <section class="rounded-2xl border border-neutral-800 bg-neutral-900 p-4">
                <h2 class="text-sm font-semibold text-neutral-50 mb-3">Créer un code</h2>
                <form action="/admin/coupons" method="post" class="grid gap-3 text-sm">
                    @csrf
                    <label class="text-neutral-300">Code
                        <input name="code" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100" placeholder="EXCLU2025" required>
                    </label>
                    <label class="text-neutral-300">Type
                        <select name="type" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100">
                            <option value="percent">Pourcentage</option>
                            <option value="fixed">Montant fixe</option>
                        </select>
                    </label>
                    <label class="text-neutral-300">Valeur
                        <input name="value" type="number" step="0.01" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100" required>
                    </label>
                    <label class="text-neutral-300">Début
                        <input name="starts_at" type="date" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100">
                    </label>
                    <label class="text-neutral-300">Fin
                        <input name="ends_at" type="date" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100">
                    </label>
                    <label class="text-neutral-300">Limite d'utilisation
                        <input name="usage_limit" type="number" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100">
                    </label>
                    <label class="text-neutral-300">Statut
                        <select name="status" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-900 px-3 py-2 text-neutral-100">
                            <option value="active" selected>Actif</option>
                            <option value="inactive">Inactif</option>
                        </select>
                    </label>
                    <button class="rounded-lg bg-sky-600 text-white px-4 py-2 font-semibold">Créer</button>
                </form>
            </section>
        </div>
    </section>
@endsection
