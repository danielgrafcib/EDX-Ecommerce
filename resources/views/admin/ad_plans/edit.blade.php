@extends('layouts.admin')
@section('content')
    <section class="space-y-6 max-w-2xl">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-neutral-50">Modifier le plan</h1>
                <p class="text-sm text-neutral-400">Ajustez le nom, le slug, le prix et l’activation du plan.</p>
            </div>
            <a href="{{ route('admin.ad_plans.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200 underline">
                Retour à la liste
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

        @if(session('status'))
            <div class="rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="post" action="{{ route('admin.ad_plans.update', $plan->id) }}" class="space-y-4">
            @csrf
            @method('put')

            <label class="block text-xs font-medium text-neutral-300">
                Nom du plan
                <input name="name"
                       value="{{ old('name', $plan->name) }}"
                       class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50"
                       required>
            </label>

            <label class="block text-xs font-medium text-neutral-300">
                Slug
                <input name="slug"
                       value="{{ old('slug', $plan->slug) }}"
                       class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50"
                       required>
            </label>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-xs font-medium text-neutral-300">
                    Prix
                    <input type="number" step="0.01" min="0"
                           name="price"
                           value="{{ old('price', $plan->price) }}"
                           class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50"
                           required>
                </label>
                <label class="block text-xs font-medium text-neutral-300">
                    Période de facturation
                    <input name="billing_period"
                           value="{{ old('billing_period', $plan->billing_period) }}"
                           class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50"
                           required>
                </label>
            </div>

            <label class="inline-flex items-center gap-2 text-xs font-medium text-neutral-300">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       class="rounded border-neutral-600 bg-neutral-950 text-sky-500"
                    @checked(old('is_active', $plan->is_active))>
                Plan actif
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">
                    Enregistrer
                </button>
                <a href="{{ route('admin.ad_plans.index') }}" class="text-xs text-neutral-400 hover:text-neutral-200">
                    Annuler
                </a>
            </div>
        </form>
    </section>
@endsection


