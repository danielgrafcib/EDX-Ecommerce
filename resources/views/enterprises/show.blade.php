@extends('layouts.app')
@section('content')
    <section class="bg-neutral-50 border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                @if($enterprise->logo_path)
                    <img src="{{ $enterprise->logo_path }}" alt="{{ $enterprise->name }}" class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-sm">
                @else
                    <div class="h-24 w-24 rounded-full bg-white border-4 border-white shadow-sm flex items-center justify-center text-neutral-400 text-xl font-bold">
                        {{ substr($enterprise->name, 0, 2) }}
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-neutral-900">{{ $enterprise->name }}</h1>
                        @if($enterprise->status === 'approved')
                            <span class="px-2 py-1 rounded-full bg-sky-100 text-sky-700 text-xs font-semibold">Vérifié</span>
                        @endif
                    </div>
                    @if($enterprise->location)
                        <p class="text-neutral-500 flex items-center gap-1 mt-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $enterprise->location }}
                        </p>
                    @endif
                    <p class="mt-2 max-w-2xl text-neutral-600">{{ $enterprise->description }}</p>
                    
                    @if($enterprise->website)
                        <div class="mt-4">
                            <a href="{{ $enterprise->website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sky-600 hover:text-sky-500 font-medium text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Visiter le site web
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-xl font-semibold text-neutral-900 mb-6">Produits de {{ $enterprise->name }}</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse($products as $product)
                <a href="/product/{{ $product->id }}" class="group block">
                    <div class="aspect-[4/5] rounded-xl bg-neutral-100 overflow-hidden relative">
                        @if($product->images->first())
                            <img src="{{ $product->images->first()->path }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-neutral-400">No Image</div>
                        @endif
                        @if($product->price_promo)
                             <div class="absolute top-2 right-2 rounded-full bg-red-500 px-2 py-1 text-[10px] font-bold text-white shadow-sm">PROMO</div>
                        @endif
                    </div>
                    <div class="mt-3">
                        <h3 class="text-sm font-medium text-neutral-900 truncate">{{ $product->name }}</h3>
                        <p class="mt-1 text-sm font-semibold text-neutral-900">
                            @if($product->price_promo)
                                <span class="text-red-600">{{ number_format($product->price_promo, 2) }} €</span>
                                <span class="text-neutral-400 line-through text-xs ml-1">{{ number_format($product->price, 2) }} €</span>
                            @else
                                {{ number_format($product->price, 2) }} €
                            @endif
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-12 text-center text-neutral-500 bg-white rounded-xl border border-dashed border-neutral-200">
                    Cette entreprise n'a pas encore de produits en ligne.
                </div>
            @endforelse
        </div>
        
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </section>
@endsection
