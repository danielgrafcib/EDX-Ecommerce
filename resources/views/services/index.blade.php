@extends('layouts.app')
@section('content')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
            <header class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-semibold text-neutral-900">Les services</h1>
                    <p class="text-sm text-neutral-600">Trouvez rapidement un service près de chez vous.</p>
                </div>
            </header>

            <form method="get" action="/services" class="grid gap-3 md:grid-cols-5">
                <input name="q" value="{{ request('q') }}" placeholder="Recherche (ex: mécanicien)" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                <input name="category" value="{{ request('category') }}" placeholder="Catégorie (ex: Plombier)" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                <input name="location" value="{{ request('location') }}" placeholder="Localisation (ex: Lomé)" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                <select name="available" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    <option value="">Disponibilité</option>
                    <option value="1" @selected(request('available')==='1')>Disponible</option>
                    <option value="0" @selected(request('available')==='0')>Indisponible</option>
                </select>
                <button class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">Filtrer</button>
                <div class="grid gap-3 md:grid-cols-3 md:col-span-5">
                    <input name="price_min" value="{{ request('price_min') }}" type="number" step="0.01" placeholder="Prix min" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    <input name="price_max" value="{{ request('price_max') }}" type="number" step="0.01" placeholder="Prix max" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                    <input name="rating_min" value="{{ request('rating_min') }}" type="number" step="0.1" placeholder="Note min" class="rounded-lg border border-neutral-300 px-3 py-2 text-sm">
                </div>
            </form>

            <div class="grid gap-6 md:grid-cols-3">
                @forelse($services as $s)
                    <div class="rounded-xl border border-neutral-200 bg-white p-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-neutral-900">{{ $s->name }}</h2>
                            <span class="text-xs rounded-full px-2 py-1 {{ $s->plan === 'premium' ? 'bg-purple-100 text-purple-700' : ($s->plan === 'standard' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                {{ ucfirst($s->plan) }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-neutral-600">{{ $s->category ?? '—' }} • {{ $s->location ?? '—' }}</p>
                        <p class="mt-2 text-sm text-neutral-800">{{ $s->description }}</p>
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span class="text-neutral-700">{{ $s->price ? number_format($s->price,2,',',' ') . ' €' : '—' }}</span>
                            <span class="text-neutral-600">{{ $s->rating ? (number_format($s->rating,1,',',' ') . '★') : '—' }}</span>
                        </div>
                        <div class="mt-3">
                            @if($s->is_available)
                                <span class="text-xs text-emerald-600">Disponible</span>
                            @else
                                <span class="text-xs text-red-600">Indisponible</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-3 text-center text-neutral-600 text-sm">Aucun service trouvé.</div>
                @endforelse
            </div>
            <div class="text-right text-xs text-neutral-500">{{ $services->links() }}</div>
        </div>
    </div>
@endsection

