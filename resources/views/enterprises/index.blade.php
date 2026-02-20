@extends('layouts.app')
@section('content')
    <section class="max-w-7xl mx-auto px-4 py-10 space-y-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Espace entreprises</p>
                <h1 class="text-3xl md:text-4xl font-semibold mt-2 text-neutral-900">Diffusez vos offres sur une vitrine e‑commerce premium</h1>
                <p class="mt-2 text-neutral-600">
                    Que vous soyez indépendant, PME ou grande marque, choisissez un plan publicitaire adapté à vos objectifs :
                    test de visibilité, présence régulière, crédibilité, performance ou domination de catégorie.
                </p>
            </div>
            <div class="rounded-2xl border border-sky-100 bg-sky-50 px-5 py-4 text-sm text-sky-900 max-w-sm">
                <p class="font-semibold">5 plans clairs pour tous les types d’annonceurs</p>
                <p class="mt-1 text-sky-900/80">
                    Individu • Générique • Premium • Pro • Entreprise.
                </p>
                <p class="mt-2 text-xs text-sky-900/70">
                    Positionnement simple : Tester sans risque, Présence régulière, Crédibilité, Performance, Partenariat & exclusivité.
                </p>
            </div>
        </div>

        @if(isset($adPlans) && $adPlans->count())
            <section class="space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-neutral-900">Plans publicitaires</h2>
                        <p class="text-sm text-neutral-600">
                            Comparez les fonctionnalités clés et choisissez le niveau de visibilité qui correspond à votre stratégie.
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-3xl border border-neutral-200 bg-white shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wide">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wide">Idéal pour</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wide">Emplacements & visibilité</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wide">Ciblage & tracking</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wide">Optimisation & reporting</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-neutral-500 uppercase tracking-wide">Tarif indicatif</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                        @foreach($adPlans as $plan)
                            @php
                                $f = (array) ($plan->features_json ?? []);
                            @endphp
                            <tr class="align-top">
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-neutral-900">{{ $plan->name }}</div>
                                    <div class="mt-1 inline-flex items-center rounded-full border border-neutral-200 px-2 py-0.5 text-[11px] text-neutral-600">
                                        {{ ucfirst($plan->billing_period) }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-neutral-700">
                                    {{ $f['target'] ?? 'Annonceurs' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-neutral-700 space-y-1">
                                    @if(!empty($f['locations'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Emplacements :</span>
                                            {{ is_array($f['locations']) ? implode(', ', $f['locations']) : $f['locations'] }}
                                        </div>
                                    @endif
                                    @if(!empty($f['rotation'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Rotation :</span> {{ $f['rotation'] }}</div>
                                    @endif
                                    @if(!empty($f['duration'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Durée min. :</span> {{ $f['duration'] }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-neutral-700 space-y-1">
                                    @if(!empty($f['targeting'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Ciblage :</span>
                                            {{ is_array($f['targeting']) ? implode(', ', $f['targeting']) : $f['targeting'] }}
                                        </div>
                                    @endif
                                    @if(!empty($f['tracking'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Tracking :</span>
                                            {{ is_array($f['tracking']) ? implode(', ', $f['tracking']) : $f['tracking'] }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-neutral-700 space-y-1">
                                    @if(!empty($f['optimization'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Optimisation :</span> {{ $f['optimization'] }}</div>
                                    @endif
                                    @if(!empty($f['reporting'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Reporting :</span> {{ $f['reporting'] }}</div>
                                    @endif
                                    @if(!empty($f['bonus'] ?? null))
                                        <div><span class="font-semibold text-neutral-900">Bonus :</span> {{ $f['bonus'] }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-right align-middle">
                                    <div class="text-base font-semibold text-neutral-900">
                                        {{ number_format($plan->price, 2, ',', ' ') }} €
                                    </div>
                                    <div class="mt-1 text-xs text-neutral-500">
                                        Tarif indicatif, sur devis pour les besoins spécifiques.
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-neutral-600">
                        <span class="font-semibold">Lecture rapide :</span>
                        Individu = tester sans risque • Générique = présence régulière • Premium = visibilité + crédibilité •
                        Pro = performance • Entreprise = partenariat & exclusivité.
                    </p>
                    <a href="/services" class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-xs font-semibold text-white">
                        Discuter d’un plan sur-mesure
                        <span>→</span>
                    </a>
                </div>
            </section>
        @endif

        <div class="border-t border-neutral-200 pt-8 mt-4 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($enterprises as $e)
                <article class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-neutral-900">
                                <a href="/enterprises/{{ $e->slug }}" class="hover:text-sky-600">{{ $e->name }}</a>
                            </h2>
                            <p class="mt-1 text-sm text-neutral-500">{{ $e->location ?? 'Lieu non spécifié' }}</p>
                        </div>
                        @if($e->logo_path)
                            <img src="{{ $e->logo_path }}" alt="{{ $e->name }}" class="h-12 w-12 rounded-full object-cover border border-neutral-100">
                        @else
                            <div class="h-12 w-12 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400 text-xs font-bold">
                                {{ substr($e->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                    
                    <p class="mt-4 text-sm text-neutral-700 line-clamp-3">{{ $e->description }}</p>

                    <div class="mt-6 flex items-center justify-between">
                        <span class="text-xs text-neutral-500">{{ $e->products_count }} produits</span>
                        <a href="/enterprises/{{ $e->slug }}" class="text-sm font-semibold text-sky-600 hover:text-sky-500">
                            Visiter la page &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $enterprises->links() }}
        </div>
    </section>
@endsection
