@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Modifier l’entreprise</h1>
                <p class="text-sm text-neutral-400">Fiche publique + produits liés (base du dashboard entreprise).</p>
            </div>
            <a href="{{ route('admin.enterprises.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200 underline">Retour à la liste</a>
        </header>

        @if ($errors->any())
            <div class="rounded-xl border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.enterprises.update', $enterprise->id) }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-xs font-medium text-neutral-300">
                    Nom
                    <input name="name" value="{{ old('name', $enterprise->name) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Slug
                    <input name="slug" value="{{ old('slug', $enterprise->slug) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-xs font-medium text-neutral-300">
                    Lieu
                    <input name="location" value="{{ old('location', $enterprise->location) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Site web
                    <input name="website" type="url" value="{{ old('website', $enterprise->website) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="text-xs font-medium text-neutral-300 space-y-2">
                    <div>Logo actuel</div>
                    <div class="rounded-xl border border-neutral-800 p-3 flex items-center gap-3">
                        @if($enterprise->logo_path)
                            <img src="{{ $enterprise->logo_path }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <span class="text-neutral-500">Aucun logo</span>
                        @endif
                        <div class="flex-1 text-neutral-400 text-xs">
                            Utilisé sur la fiche publique entreprise et les publicités associées.
                        </div>
                    </div>
                    <label class="block">
                        Nouveau logo (optionnel)
                        <input type="file" name="logo" accept="image/*" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                    </label>
                </div>
                <div class="space-y-3">
                    <label class="text-xs font-medium text-neutral-300 block">
                        Statut
                        <select name="status" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                            <option value="pending" @selected(old('status', $enterprise->status)==='pending')>En attente</option>
                            <option value="approved" @selected(old('status', $enterprise->status)==='approved')>Approuvée</option>
                            <option value="rejected" @selected(old('status', $enterprise->status)==='rejected')>Rejetée</option>
                        </select>
                    </label>
                    <label class="inline-flex items-center gap-2 text-xs font-medium text-neutral-300">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-600 bg-neutral-950 text-sky-500" @checked(old('is_active', $enterprise->is_active))>
                        Entreprise active
                    </label>
                </div>
            </div>
            <label class="text-xs font-medium text-neutral-300 block">
                Description
                <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">{{ old('description', $enterprise->description) }}</textarea>
            </label>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Enregistrer</button>
                <a href="{{ route('admin.enterprises.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Annuler</a>
            </div>
        </form>

        </form>

        <section class="space-y-3">
            <h2 class="text-sm font-semibold text-neutral-200">Abonnement Publicitaire</h2>
            <div class="rounded-2xl border border-neutral-800 bg-neutral-900 p-6">
                @php
                    $currentSub = $enterprise->subscriptions()->where('status', 'active')->latest()->first();
                @endphp
                
                @if($currentSub)
                    <div class="flex items-center justify-between mb-6 border-b border-neutral-800 pb-4">
                        <div>
                            <p class="text-xs text-neutral-400 uppercase tracking-wide">Plan Actuel</p>
                            <h3 class="text-2xl font-bold text-white mt-1">{{ optional($currentSub->plan)->name }}</h3>
                            <div class="flex gap-2 mt-2 text-xs">
                                <span class="bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">Actif</span>
                                <span class="text-neutral-500">Depuis le {{ $currentSub->start_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xl font-semibold text-neutral-200">{{ number_format(optional($currentSub->plan)->price, 2) }} €</p>
                            <p class="text-xs text-neutral-500">/ {{ optional($currentSub->plan)->billing_period }}</p>
                        </div>
                    </div>
                @else
                    <div class="mb-6 p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 text-yellow-200 text-sm flex items-center gap-3">
                        <span class="text-xl">⚠️</span>
                        <p>Cette entreprise n'a aucun plan publicitaire actif. Elle ne bénéficie pas de visibilité sponsorisée.</p>
                    </div>
                @endif
    
                <form action="{{ route('admin.enterprises.subscribe', $enterprise->id) }}" method="POST">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-3 items-end">
                        <label class="block text-xs font-medium text-neutral-300">
                            Choisir un plan
                            <select name="plan_id" class="mt-1 block w-full rounded-lg bg-neutral-950 border-neutral-700 text-neutral-200 text-sm py-2">
                                @foreach($adPlans as $plan)
                                    <option value="{{ $plan->id }}" @selected($currentSub && $currentSub->ad_plan_id == $plan->id)>
                                        {{ $plan->name }} — {{ number_format($plan->price, 2) }} € / {{ $plan->billing_period }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-xs font-medium text-neutral-300">
                             Période de facturation
                            <select name="billing_period" class="mt-1 block w-full rounded-lg bg-neutral-950 border-neutral-700 text-neutral-200 text-sm py-2">
                                 <option value="monthly">Mensuelle</option>
                                 <option value="yearly">Annuelle</option>
                            </select>
                        </label>
                        <button class="px-4 py-2 bg-sky-600 hover:bg-sky-500 text-white rounded-lg text-sm font-semibold transition">
                            {{ $currentSub ? 'Mettre à jour l\'abonnement' : 'Activer l\'abonnement' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-semibold text-neutral-200">Produits rattachés</h2>
            <div class="rounded-2xl border border-neutral-800 overflow-hidden">
                <table class="w-full text-xs">
                    <thead class="bg-neutral-950 text-neutral-400">
                    <tr>
                        <th class="px-3 py-2 text-left">Produit</th>
                        <th class="px-3 py-2 text-left">Catégorie</th>
                        <th class="px-3 py-2 text-left">Prix</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($enterprise->products as $p)
                        <tr class="border-t border-neutral-800">
                            <td class="px-3 py-2">{{ $p->name }}</td>
                            <td class="px-3 py-2">{{ optional($p->category)->name ?? '—' }}</td>
                            <td class="px-3 py-2">{{ number_format($p->price, 2, ',', ' ') }} €</td>
                            <td class="px-3 py-2 text-right">
                                <form method="post" action="{{ route('admin.enterprises.detach', [$enterprise->id, $p->id]) }}" class="inline" onsubmit="return confirm('Détacher ce produit ?');">
                                    @csrf
                                    @method('delete')
                                    <button class="px-2 py-1 rounded border border-neutral-700 text-neutral-200">Détacher</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-4 text-center text-neutral-500">Aucun produit rattaché pour le moment.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <h3 class="text-xs font-semibold text-neutral-300 mt-4">Ajouter un produit à cette entreprise</h3>
            <div class="rounded-2xl border border-neutral-800 overflow-hidden">
                <table class="w-full text-xs">
                    <thead class="bg-neutral-950 text-neutral-400">
                    <tr>
                        <th class="px-3 py-2 text-left">Produit disponible</th>
                        <th class="px-3 py-2 text-left">Catégorie</th>
                        <th class="px-3 py-2 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $p)
                        <tr class="border-t border-neutral-800">
                            <td class="px-3 py-2">{{ $p->name }}</td>
                            <td class="px-3 py-2">{{ optional($p->category)->name ?? '—' }}</td>
                            <td class="px-3 py-2 text-right">
                                <form method="post" action="{{ route('admin.enterprises.attach', $enterprise->id) }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <button class="px-2 py-1 rounded border border-neutral-700 text-neutral-200">Attacher</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-neutral-500">Aucun produit disponible à rattacher.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-2 text-right text-xs text-neutral-500">
                {{ $products->links() }}
            </div>
        </section>
    </section>
@endsection

