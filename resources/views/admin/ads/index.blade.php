@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Publicités</h1>
                <p class="text-sm text-neutral-400">
                    Suivez et optimisez les visuels sponsorisés affichés sur la page d’accueil et les zones premium.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <form method="get" action="{{ route('admin.ads.index') }}" class="flex flex-wrap items-center gap-2">
                    <input name="q" value="{{ request('q') }}" placeholder="Recherche titre" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                    <select name="active" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                        <option value="">Tous</option>
                        <option value="1" @selected(request('active')==='1')>Actifs</option>
                        <option value="0" @selected(request('active')==='0')>Inactifs</option>
                    </select>
                    <select name="ad_type" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                        <option value="">Tous types</option>
                        <option value="banner" @selected(request('ad_type')==='banner')>Bannière</option>
                        <option value="company" @selected(request('ad_type')==='company')>Entreprise</option>
                        <option value="service" @selected(request('ad_type')==='service')>Service</option>
                        <option value="shop" @selected(request('ad_type')==='shop')>Boutique</option>
                        <option value="category_sponsor" @selected(request('ad_type')==='category_sponsor')>Catégorie</option>
                    </select>
                    <select name="payment_model" class="rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-1.5 text-sm">
                        <option value="">Tous paiements</option>
                        <option value="daily" @selected(request('payment_model')==='daily')>Par jour</option>
                        <option value="click" @selected(request('payment_model')==='click')>Par clic</option>
                        <option value="monthly" @selected(request('payment_model')==='monthly')>Par mois</option>
                        <option value="subscription_premium" @selected(request('payment_model')==='subscription_premium')>Abonnement Premium/Pro</option>
                    </select>
                    <button class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Filtrer</button>
                </form>
                <a href="{{ route('admin.ads.create') }}" class="rounded-xl border border-neutral-700 px-3 py-1.5 text-sm">Nouvelle publicité</a>
            </div>
        </header>

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-neutral-800 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-neutral-950 text-neutral-400">
                <tr>
                    <th class="px-3 py-2 text-left">Titre</th>
                    <th class="px-3 py-2 text-left">Type média</th>
                    <th class="px-3 py-2 text-left">Type pub</th>
                    <th class="px-3 py-2 text-left">Paiement</th>
                    <th class="px-3 py-2 text-left">Aperçu</th>
                    <th class="px-3 py-2 text-left">Prix</th>
                    <th class="px-3 py-2 text-left">Actif</th>
                    <th class="px-3 py-2 text-left">Période</th>
                    <th class="px-3 py-2 text-left">Stats</th>
                    <th class="px-3 py-2 text-left">Ordre</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($ads as $ad)
                    <tr class="border-t border-neutral-800">
                        <td class="px-3 py-2">{{ $ad->title }}</td>
                        <td class="px-3 py-2">{{ strtoupper($ad->media_type) }}</td>
                        <td class="px-3 py-2 text-xs">
                            @switch($ad->ad_type)
                                @case('company') Pub entreprise @break
                                @case('service') Service @break
                                @case('shop') Boutique @break
                                @case('category_sponsor') Catégorie @break
                                @default Bannière
                            @endswitch
                        </td>
                        <td class="px-3 py-2 text-xs">
                            @switch($ad->payment_model)
                                @case('daily') Jour @break
                                @case('click') Clic @break
                                @case('monthly') Mois @break
                                @case('subscription_premium') Abonnement @break
                                @default —
                            @endswitch
                        </td>
                        <td class="px-3 py-2">
                            @php
                                $path = trim((string)$ad->media_path);
                                $isExternal = \Illuminate\Support\Str::startsWith($path, ['http://','https://']);
                                $ver = optional($ad->updated_at)->getTimestamp() ?? time();
                                $mediaUrl = $path ? ($isExternal ? $path : url('/promo/media/'.$ad->id).'?v='.$ver) : null;
                            @endphp
                            @if($mediaUrl)
                                @if($ad->media_type==='image')
                                    <img src="{{ $mediaUrl }}" alt="" class="h-10 w-16 rounded object-cover">
                                @else
                                    <video src="{{ $mediaUrl }}" class="h-10 w-16 rounded" muted></video>
                                @endif
                            @else
                                <span class="text-neutral-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            @if(!is_null($ad->price))
                                {{ number_format($ad->price, 2, ',', ' ') }} €
                            @else
                                <span class="text-neutral-500">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $ad->is_active ? 'Oui' : 'Non' }}</td>
                        <td class="px-3 py-2 text-xs">
                            @if($ad->start_date || $ad->end_date)
                                {{ optional($ad->start_date)->format('d/m/Y') ?? '—' }}
                                –
                                {{ optional($ad->end_date)->format('d/m/Y') ?? '—' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs">
                            Vues: {{ $ad->views_count }}<br>
                            Clics: {{ $ad->clicks_count }}
                        </td>
                        <td class="px-3 py-2">{{ $ad->sort_order }}</td>
                        <td class="px-3 py-2 text-right">
                            <a href="{{ route('admin.ads.edit', $ad->id) }}" class="text-sky-400">Modifier</a>
                            <span class="mx-2 text-neutral-700">|</span>
                            <form action="{{ route('admin.ads.toggle', $ad->id) }}" method="post" class="inline">
                                @csrf
                                @method('put')
                                <button class="text-xs px-2 py-1 rounded border border-neutral-700">{{ $ad->is_active ? 'Désactiver' : 'Activer' }}</button>
                            </form>
                            <span class="mx-2 text-neutral-700">|</span>
                            <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="post" class="inline" onsubmit="return confirm('Supprimer cette publicité ?');">
                                @csrf
                                @method('delete')
                                <button class="text-xs px-2 py-1 rounded border border-red-600/40 text-red-300">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-3 py-6 text-center text-neutral-500">Aucune publicité trouvée</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $ads->links() }}</div>
    </section>
@endsection

