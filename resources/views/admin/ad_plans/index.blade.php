@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Plans publicitaires</h1>
                <p class="text-sm text-neutral-400">Gérez les prix et l’activation de vos offres (Individu, Générique, Premium, Pro, Entreprise).</p>
            </div>
        </header>

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-neutral-800 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-neutral-950 text-neutral-400">
                <tr>
                    <th class="px-3 py-2 text-left">Nom</th>
                    <th class="px-3 py-2 text-left">Slug</th>
                    <th class="px-3 py-2 text-left">Période</th>
                    <th class="px-3 py-2 text-right">Prix</th>
                    <th class="px-3 py-2 text-left">Actif</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($plans as $plan)
                    <tr class="border-t border-neutral-800">
                        <td class="px-3 py-2 font-semibold text-neutral-100">{{ $plan->name }}</td>
                        <td class="px-3 py-2 text-xs text-neutral-500">{{ $plan->slug }}</td>
                        <td class="px-3 py-2 text-xs">{{ $plan->billing_period }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($plan->price, 2, ',', ' ') }} €</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]
                                {{ $plan->is_active ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/30' : 'bg-neutral-800 text-neutral-400 border border-neutral-700' }}">
                                {{ $plan->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.ad_plans.edit', $plan->id) }}" class="text-xs text-sky-400 hover:text-sky-300">
                                Modifier
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-neutral-500">
                            Aucun plan configuré.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection


