@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Nouvelle entreprise</h1>
                <p class="text-sm text-neutral-400">
                    Créez une fiche entreprise partenaire qui pourra ensuite souscrire à un plan publicitaire (Individu, Générique, Premium, Pro, Entreprise).
                </p>
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

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.5fr),minmax(0,1fr)] items-start">
            <form method="post" action="{{ route('admin.enterprises.store') }}" class="space-y-6" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-xs font-medium text-neutral-300">
                        Nom
                        <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                    </label>
                    <label class="text-xs font-medium text-neutral-300">
                        Slug
                        <input name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-xs font-medium text-neutral-300">
                        Lieu
                        <input name="location" value="{{ old('location') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                    </label>
                    <label class="text-xs font-medium text-neutral-300">
                        Site web
                        <input name="website" type="url" value="{{ old('website') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-xs font-medium text-neutral-300">
                        Logo (optionnel)
                        <input type="file" name="logo" accept="image/*" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                    </label>
                    <label class="text-xs font-medium text-neutral-300">
                        Statut
                        <select name="status" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                            <option value="pending" @selected(old('status','pending')==='pending')>En attente</option>
                            <option value="approved" @selected(old('status')==='approved')>Approuvée</option>
                            <option value="rejected" @selected(old('status')==='rejected')>Rejetée</option>
                        </select>
                    </label>
                </div>
                <label class="text-xs font-medium text-neutral-300 block">
                    Description
                    <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">{{ old('description') }}</textarea>
                </label>
                <label class="inline-flex items-center gap-2 text-xs font-medium text-neutral-300">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-600 bg-neutral-950 text-sky-500" @checked(old('is_active', true))>
                    Entreprise active
                </label>
                <div class="flex items-center gap-3">
                    <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Enregistrer</button>
                    <a href="{{ route('admin.enterprises.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">Annuler</a>
                </div>
            </form>

            <aside class="rounded-2xl border border-neutral-800 bg-neutral-950 p-4 text-xs text-neutral-200 space-y-2">
                <p class="font-semibold text-neutral-50 text-sm">Comment vendre l’offre publicité</p>
                <p>
                    1. Créez la fiche entreprise (cette page).<br>
                    2. Dans la fiche, attribuez un <strong>plan publicitaire</strong> (Individu, Générique, Premium, Pro, Entreprise).<br>
                    3. Créez ensuite une ou plusieurs <strong>publicités</strong> reliées à cette entreprise.
                </p>
                <ul class="mt-2 space-y-1">
                    <li><strong>Individu</strong> = tester sans risque (petit budget, campagne courte).</li>
                    <li><strong>Générique</strong> = présence régulière sur le site.</li>
                    <li><strong>Premium</strong> = visibilité + crédibilité (emplacements haut de page).</li>
                    <li><strong>Pro</strong> = performance (leads/ventes, A/B test, optimisation continue).</li>
                    <li><strong>Entreprise</strong> = partenariat & exclusivité (takeover, retargeting, QBR).</li>
                </ul>
                <a href="{{ route('admin.ad_plans.index') }}" class="inline-flex items-center gap-1 text-sky-400 hover:text-sky-300 mt-1">
                    Voir la grille des plans →
                </a>
            </aside>
        </div>
    </section>
@endsection

