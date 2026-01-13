@extends('layouts.app')
@section('content')
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Réseau</p>
                <h1 class="text-3xl md:text-4xl font-semibold mt-2 text-neutral-900">Nos partenariats</h1>
                <p class="mt-2 text-neutral-600">Nous présentons des sociétés partenaires et leurs produits. Nous jouons le rôle d’intermédiaire: mise en relation, exposition et information.</p>
            </div>
            <a href="/catalog" class="hidden md:inline-flex items-center rounded-full border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:border-sky-600">Voir la boutique</a>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
            @foreach($partners as $p)
                <article class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-neutral-900">{{ $p->name }}</h2>
                            <p class="mt-1 text-sm text-neutral-500">{{ $p->location }}</p>
                        </div>
                        @if(!empty($p->website))
                            <a href="{{ $p->website }}" target="_blank" rel="noopener" class="text-sm font-semibold text-sky-600">Site →</a>
                        @endif
                    </div>
                    <p class="mt-4 text-neutral-700">{{ $p->description }}</p>

                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <section>
                            <h3 class="text-sm uppercase tracking-wide text-neutral-500 font-semibold">Produits présentés</h3>
                            <ul class="mt-2 space-y-2 text-sm">
                                @foreach($p->products->take(5) as $prod)
                                    <li class="flex items-center justify-between rounded-2xl border border-neutral-200 px-3 py-2">
                                        <a href="/product/{{ $prod->id }}" class="text-neutral-700 hover:text-sky-600">{{ $prod->name }}</a>
                                        <span class="text-neutral-500">{{ optional($prod->category)->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                        <section>
                            <h3 class="text-sm uppercase tracking-wide text-neutral-500 font-semibold">Articles</h3>
                            <ul class="mt-2 space-y-2 text-sm">
                                @foreach($p->articles->take(5) as $a)
                                    <li>
                                        <a href="#" class="inline-flex items-center gap-2 text-neutral-700 hover:text-sky-600">
                                            <span>📰</span>
                                            <span>{{ $a->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
