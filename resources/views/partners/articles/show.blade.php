@extends('layouts.app')
@section('content')
    <section class="max-w-7xl mx-auto px-4 py-10">
        <a href="/partners/{{ $partner->slug }}/articles" class="text-sm text-neutral-500 hover:text-neutral-900">← Retour aux articles</a>
        <div class="mt-4 grid gap-8 lg:grid-cols-[1fr,320px]">
            <article class="rounded-3xl border border-neutral-100 bg-white p-6">
                <h1 class="text-3xl font-semibold text-neutral-900">{{ $article->title }}</h1>
                <p class="mt-1 text-sm text-neutral-500">{{ optional($article->published_at)->format('d/m/Y') }}</p>
                @if($article->cover_path)
                    <img src="{{ $article->cover_path }}" class="mt-6 w-full rounded-2xl object-cover" />
                @endif
                <div class="prose max-w-none mt-6">{!! nl2br(e($article->content)) !!}</div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    @foreach($article->images as $img)
                        <img src="{{ $img->path }}" class="w-full rounded-2xl object-cover" />
                    @endforeach
                </div>
            </article>
            <aside class="rounded-3xl border border-neutral-100 bg-white p-6 h-fit">
                <h3 class="text-lg font-semibold">{{ $partner->name }}</h3>
                <p class="mt-2 text-sm text-neutral-600">{{ $partner->location }}</p>
                <p class="mt-2 text-sm text-neutral-600">{{ $partner->description }}</p>
                @if($partner->website)
                    <a href="{{ $partner->website }}" target="_blank" class="mt-3 inline-flex items-center rounded-full border border-neutral-300 px-3 py-1.5 text-sm text-neutral-700 hover:border-sky-600">Site</a>
                @endif
                <div class="mt-6">
                    <h4 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Catégories</h4>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        @foreach($article->categories as $c)
                            <span class="rounded-full bg-sky-50 text-sky-700 px-2 py-1">{{ $c->name }}</span>
                        @endforeach
                    </div>
                    <h4 class="mt-4 text-sm font-semibold uppercase tracking-wide text-neutral-500">Tags</h4>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        @foreach($article->tags as $t)
                            <span class="rounded-full bg-neutral-100 text-neutral-700 px-2 py-1">#{{ $t->name }}</span>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
