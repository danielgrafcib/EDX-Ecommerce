@extends('layouts.app')
@section('content')
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Articles</p>
                <h1 class="text-3xl md:text-4xl font-semibold mt-2 text-neutral-900">{{ $partner->name }}</h1>
                <p class="mt-2 text-neutral-600">{{ $partner->description }}</p>
            </div>
            @if($partner->website)
                <a href="{{ $partner->website }}" target="_blank" class="hidden md:inline-flex items-center rounded-full border border-neutral-300 px-4 py-2 text-sm text-neutral-700 hover:border-sky-600">Site</a>
            @endif
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($articles as $a)
                <article class="rounded-3xl border border-neutral-100 bg-white p-5 shadow-sm hover:shadow-lg transition flex flex-col gap-4">
                    @if($a->cover_path)
                        <img src="{{ $a->cover_path }}" alt="{{ $a->title }}" class="aspect-[4/3] w-full rounded-2xl object-cover" />
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-neutral-900">
                            <a href="/partners/{{ $partner->slug }}/articles/{{ $a->id }}" class="hover:text-sky-600">{{ $a->title }}</a>
                        </h2>
                        <p class="mt-1 text-sm text-neutral-500">{{ optional($a->published_at)->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs">
                        @foreach($a->categories as $c)
                            <span class="rounded-full bg-sky-50 text-sky-700 px-2 py-1">{{ $c->name }}</span>
                        @endforeach
                        @foreach($a->tags as $t)
                            <span class="rounded-full bg-neutral-100 text-neutral-700 px-2 py-1">#{{ $t->name }}</span>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-3xl border border-dashed border-neutral-200 bg-neutral-50 p-10 text-center text-neutral-600">
                    Aucun article pour ce partenaire.
                </div>
            @endforelse
        </div>
    </section>
@endsection
