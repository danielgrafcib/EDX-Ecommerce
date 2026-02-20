@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Nouvelle publicité</h1>
                <p class="text-sm text-neutral-400">
                    Créez un visuel sponsorisé relié à une entreprise et à un plan publicitaire (Individu, Générique, Premium, Pro, Entreprise).
                </p>
            </div>
            <a href="{{ route('admin.ad_plans.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200 underline">
                Gérer les plans
            </a>
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

        <div>
            <form method="post" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-sm text-neutral-400 block">
                        Titre
                        <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                    </label>
                    <label class="text-sm text-neutral-400 block">
                        Type de média
                        <select name="media_type" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                            <option value="image" @selected(old('media_type')==='image')>Image (bannière)</option>
                            <option value="video" @selected(old('media_type')==='video')>Vidéo</option>
                        </select>
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    <label class="text-sm text-neutral-400 block">
                        Type de publicité
                        <select name="ad_type" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                            <option value="banner" @selected(old('ad_type')==='banner')>Bannière générique</option>
                            <option value="company" @selected(old('ad_type')==='company')>Publicité entreprise (brand)</option>
                            <option value="service" @selected(old('ad_type')==='service')>Service mis en avant</option>
                            <option value="shop" @selected(old('ad_type')==='shop')>Boutique sponsorisée</option>
                            <option value="category_sponsor" @selected(old('ad_type')==='category_sponsor')>Sponsoring de catégorie</option>
                        </select>
                    </label>
                    <label class="text-sm text-neutral-400 block">
                        Modèle de paiement
                        <select name="payment_model" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
                            <option value="">— Non défini —</option>
                            <option value="daily" @selected(old('payment_model')==='daily')>Par jour (campagne courte)</option>
                            <option value="click" @selected(old('payment_model')==='click')>Par clic (performance)</option>
                            <option value="monthly" @selected(old('payment_model')==='monthly')>Par mois (présence continue)</option>
                            <option value="subscription_premium" @selected(old('payment_model')==='subscription_premium')>Abonnement Premium / Pro</option>
                        </select>
                    </label>
                    <div class="grid gap-2">
                        <label class="text-sm text-neutral-400 block">
                            Début
                            <input name="start_date" type="datetime-local" value="{{ old('start_date') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm">
                        </label>
                        <label class="text-sm text-neutral-400 block">
                            Fin
                            <input name="end_date" type="datetime-local" value="{{ old('end_date') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm">
                        </label>
                    </div>
                    <label class="text-sm text-neutral-400 block">
                        Entreprise liée (optionnel)
                        <select name="enterprise_id" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
                            <option value="">— Aucune —</option>
                            @foreach($enterprises as $e)
                                <option value="{{ $e->id }}" @selected(old('enterprise_id') == $e->id)>{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <label class="text-sm text-neutral-400 block">
                    Description (optionnelle)
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2 text-sm" placeholder="Texte qui apparaîtra à côté de l'image sur la page d'accueil">{{ old('description') }}</textarea>
                </label>

                <label class="text-sm text-neutral-400 block">
                    Média
                    <input type="file" name="media" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" required>
                </label>

                <div class="grid gap-4 md:grid-cols-3">
                    <label class="text-sm text-neutral-400 block">
                        Lien (optionnel)
                        <input name="link_url" type="url" value="{{ old('link_url') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" placeholder="Lien suivi UTM vers la page de destination">
                    </label>
                    <label class="text-sm text-neutral-400 block">
                        Ordre
                        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2">
                    </label>
                    <label class="text-sm text-neutral-400 block">
                        Prix (en €)
                        <input name="price" type="number" step="0.01" min="0" value="{{ old('price') }}" class="mt-1 w-full rounded-xl bg-neutral-950 border border-neutral-800 px-3 py-2" placeholder="Ex: 250.00">
                    </label>
                </div>

                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-neutral-700" @checked(old('is_active', true))>
                    Actif
                </label>

                <p class="text-xs text-neutral-500">
                    Astuce : définissez le prix en tenant compte du <strong>type de publicité</strong> (bannière, entreprise, service)
                    et du <strong>statut de l’entreprise</strong> (plan actif ou non).
                </p>

                <div>
                    <button class="rounded-xl bg-sky-600 text-white px-4 py-2 text-sm font-semibold">Créer la publicité</button>
                    <a href="{{ route('admin.ads.index') }}" class="ml-2 text-sm text-neutral-400 hover:text-neutral-200">Annuler</a>
                </div>
            </form>
        </div>
    </section>
@endsection


