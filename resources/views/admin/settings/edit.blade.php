@extends('layouts.admin')
@section('content')
    <section class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Paramètres du site</h1>
                <p class="text-sm text-neutral-400">Nom, logo, couleur, email, paiement, frais de livraison.</p>
            </div>
        </header>
        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif
        <form method="post" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('put')
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-xs font-medium text-neutral-300">
                    Nom du site
                    <input name="site_name" value="{{ old('site_name', $fields['site_name']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Logo (URL)
                    <input name="logo_url" value="{{ old('logo_url', $fields['logo_url']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Logo (upload)
                    <input type="file" name="logo" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
            </div>
            @if(!empty($fields['logo_url']))
            <div class="rounded-lg border border-neutral-800 bg-neutral-900/50 p-3">
                <div class="text-xs font-medium text-neutral-300 mb-2">Aperçu du logo actuel</div>
                <img src="{{ $fields['logo_url'] }}" alt="Logo" class="h-10">
            </div>
            @endif
            <div class="grid gap-4 md:grid-cols-3">
                <label class="text-xs font-medium text-neutral-300">
                    Couleur primaire
                    <input name="primary_color" value="{{ old('primary_color', $fields['primary_color']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Email expéditeur
                    <input name="email_from" value="{{ old('email_from', $fields['email_from']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
                <label class="text-xs font-medium text-neutral-300">
                    Méthodes de paiement
                    <input name="payment_methods" value="{{ old('payment_methods', $fields['payment_methods']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="text-xs font-medium text-neutral-300">
                    Intervalle du slideshow (ms)
                    <input name="ads_interval_ms" value="{{ old('ads_interval_ms', $fields['ads_interval_ms']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" placeholder="3000">
                </label>
            </div>
            <label class="text-xs font-medium text-neutral-300">
                Frais de livraison (€)
                <input name="shipping_fee" type="number" step="0.01" value="{{ old('shipping_fee', $fields['shipping_fee']) }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50">
            </label>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Enregistrer</button>
            </div>
        </form>
    </section>
@endsection
