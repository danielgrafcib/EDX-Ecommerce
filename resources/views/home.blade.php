@extends('layouts.app')
@section('content')
    <section class="relative overflow-hidden border-b border-sky-100/60">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-200 via-sky-100 to-white pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-12">
            <div class="flex flex-col gap-10 lg:flex-row lg:items-center">
                <div class="lg:w-2/5 space-y-6">
                    <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-white shadow-sm text-sm font-medium border border-sky-100">
                        <span class="h-2 w-2 rounded-full bg-sky-500 animate-pulse"></span>
                        Nouveautés premium 2025
                    </div>
                    <h1 class="text-4xl md:text-5xl font-semibold tracking-tight text-neutral-900">Vitrine e-commerce nouvelle génération</h1>
                    <p class="text-lg text-neutral-700">Découvrez des lits en mousse haut de gamme, des véhicules prêts à partir et des ordinateurs calibrés pour la performance. Paiement sécurisé, livraison express.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="/catalog" class="px-6 py-3 rounded-full bg-sky-600 text-white font-semibold shadow-lg shadow-sky-200 hover:bg-sky-700">Acheter maintenant</a>
                        <a href="#categories" class="px-6 py-3 rounded-full border border-neutral-200 hover:border-sky-500 text-neutral-700 font-semibold">Explorer les catégories</a>
                    </div>
                    <dl class="grid grid-cols-3 gap-4 text-sm text-neutral-600">
                        @foreach($siteMetrics as $metric)
                            <div class="rounded-2xl border border-white/30 bg-white/60 px-4 py-3 shadow-sm">
                                <dt class="text-xs uppercase tracking-wide text-neutral-500">{{ $metric['label'] }}</dt>
                                <dd class="text-2xl font-semibold text-neutral-900">{{ $metric['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                <div class="lg:w-3/5">
                    <div class="relative rounded-3xl bg-white shadow-2xl p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold">Top ventes aujourd’hui</h3>
                            <div class="text-sm text-neutral-500">Actualisé en temps réel</div>
                        </div>
                        <div class="flex gap-6 overflow-x-auto pb-4" data-hero-slider>
                            @foreach($heroProducts as $product)
                                @php
                                    $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                                @endphp
                                <article class="min-w-[220px] rounded-2xl border border-neutral-100 bg-neutral-50/60 p-4 flex flex-col gap-3">
                                    @if($primary)
                                        <img src="{{ $primary->path }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full rounded-xl object-cover" />
                                    @else
                                        <div class="aspect-[4/3] rounded-xl bg-gradient-to-br from-neutral-100 to-white grid place-items-center text-5xl">🛒</div>
                                    @endif
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-neutral-500">{{ optional($product->category)->name ?? 'Catégorie' }}</p>
                                        <h4 class="font-semibold text-lg text-neutral-900">{{ $product->name }}</h4>
                                        <p class="text-sky-600 font-semibold">{{ number_format($product->price, 2, ',', ' ') }} €</p>
                                    </div>
                                    <a href="/product/{{ $product->id }}" class="text-sm font-medium text-neutral-900 hover:text-sky-600">Voir le produit →</a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-sky-50 border-y border-sky-100">
        <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-lg font-semibold text-neutral-900">Profitez d’un nouveau design plus dynamique avec 35% de bleu ciel.</p>
            <a href="/catalog" class="px-6 py-2 rounded-full bg-sky-600 text-white font-semibold hover:bg-sky-700">Découvrir la boutique</a>
        </div>
    </section>

    <section id="categories" class="max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Les univers phares</p>
                <h2 class="text-3xl font-semibold mt-1">Nos trois catégories stratégiques</h2>
            </div>
            <a href="/catalog" class="text-sm font-medium text-neutral-600 hover:text-sky-600">Tout voir →</a>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($categories as $category)
                <article class="rounded-3xl border border-neutral-100 bg-white p-6 shadow-sm hover:-translate-y-1 transition">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="h-12 w-12 rounded-2xl bg-sky-50 grid place-items-center text-2xl">✨</div>
                        <span class="text-xs font-semibold uppercase text-neutral-500">{{ $category->products_count }} produits</span>
                    </div>
                    <h3 class="text-xl font-semibold text-neutral-900">{{ $category->name }}</h3>
                    <p class="mt-2 text-sm text-neutral-600">{{ $category->description ?? 'Découvrez une sélection de produits premium.' }}</p>
                    <a href="/catalog?category_id={{ $category->id }}" class="mt-4 inline-flex items-center text-sm font-semibold text-sky-600">Découvrir →</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-white to-sky-50 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Promotion du moment</p>
                <h2 class="text-3xl font-semibold mt-1">Offres exclusives</h2>
            </div>
            <div class="flex gap-2 text-sm text-neutral-500">
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-400"></span> En stock</span>
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-400"></span> Dernières pièces</span>
            </div>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($promotions as $product)
                @php
                    $pimg = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                @endphp
                <article class="group rounded-3xl border border-neutral-100 bg-white p-5 shadow-sm hover:shadow-lg transition">
                    <div class="relative">
                        @if($pimg)
                            <img src="{{ $pimg->path }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full rounded-2xl object-cover" />
                        @else
                            <div class="aspect-[4/3] rounded-2xl bg-neutral-100 grid place-items-center text-5xl">📦</div>
                        @endif
                        <div class="absolute top-3 left-3 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-emerald-600">-15%</div>
                    </div>
                    <div class="mt-4 space-y-1">
                        <p class="text-xs uppercase tracking-wide text-neutral-500">{{ optional($product->category)->name ?? 'Catégorie' }}</p>
                        <h3 class="text-lg font-semibold text-neutral-900 group-hover:text-sky-600">{{ $product->name }}</h3>
                        <p class="text-neutral-700">
                            @if($product->price_promo !== null)
                                <span class="text-sm line-through text-neutral-400 mr-2">{{ number_format($product->price, 2, ',', ' ') }} €</span>
                                <span class="font-semibold text-emerald-600">{{ number_format($product->price_promo, 2, ',', ' ') }} €</span>
                            @else
                                {{ number_format($product->price, 2, ',', ' ') }} €
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <a href="/product/{{ $product->id }}" class="text-sm font-semibold text-neutral-900 hover:text-sky-600">Voir le détail</a>
                        <form action="/cart" method="post">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button class="text-sm font-semibold text-sky-600">Ajouter</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 py-12 grid gap-8 lg:grid-cols-2">
        <div class="rounded-3xl bg-neutral-900 text-white p-8">
            <p class="text-sm uppercase tracking-wide text-sky-300">Avis vérifiés</p>
            <h2 class="text-3xl font-semibold mt-2">Ils recommandent notre plateforme</h2>
            <div class="mt-8 space-y-6">
                @foreach($bestReviews as $review)
                    <figure class="rounded-2xl bg-white/10 p-5 border border-white/10">
                        <div class="flex items-center justify-between text-sm text-sky-200">
                            <span>{{ $review['name'] }}</span>
                            <span>{{ str_repeat('★', $review['rating']) }}</span>
                        </div>
                        <figcaption class="text-lg font-semibold mt-1">{{ $review['title'] }}</figcaption>
                        <blockquote class="mt-2 text-sm text-neutral-200">{{ $review['quote'] }}</blockquote>
                    </figure>
                @endforeach
            </div>
        </div>
        <div class="rounded-3xl border border-neutral-100 bg-white p-8">
            <p class="text-sm uppercase tracking-wide text-sky-600 font-semibold">Pourquoi nous choisir ?</p>
            <h2 class="text-3xl font-semibold mt-2 text-neutral-900">Expérience client omnicanal</h2>
            <ul class="mt-6 space-y-4 text-neutral-700">
                <li class="flex items-start gap-3"><span class="text-sky-600">✓</span> Parcours d’achat en 4 étapes (identification, adresse, livraison, paiement).</li>
                <li class="flex items-start gap-3"><span class="text-sky-600">✓</span> Paiements Stripe, PayPal et virement bancaire avec confirmation instantanée.</li>
                <li class="flex items-start gap-3"><span class="text-sky-600">✓</span> Compte client complet : adresses, wishlist, historique des commandes.</li>
                <li class="flex items-start gap-3"><span class="text-sky-600">✓</span> Support 7/7 avec suivi d’expédition et alertes stock.</li>
            </ul>
            <div class="mt-8 flex flex-wrap gap-3">
                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">Livraison</p>
                    <p class="text-lg font-semibold text-neutral-900">24/48h express</p>
                </div>
                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">Paiement</p>
                    <p class="text-lg font-semibold text-neutral-900">100% sécurisé</p>
                </div>
                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 px-4 py-3">
                    <p class="text-xs uppercase tracking-wide text-neutral-500">Support</p>
                    <p class="text-lg font-semibold text-neutral-900">24/7</p>
                </div>
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden mt-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="relative rounded-3xl overflow-hidden border border-neutral-100 bg-white">
                <div id="ad-slideshow-bottom" class="relative" data-interval="{{ isset($adsInterval) ? (int)$adsInterval : 3000 }}">
                    <div class="relative w-full h-[260px] sm:h-[320px] lg:h-[380px]">
                        @php
                            $adsList = ($ads ?? collect())->values();
                            $advert = null;
                        @endphp
                        @foreach($adsList as $advert)
                            @php
                                $path = trim((string)$advert->media_path);
                                $isExternal = \Illuminate\Support\Str::startsWith($path, ['http://','https://']);
                                $mediaType = strtolower($advert->media_type ?? '');
                                $isVideo = $mediaType === 'video' || \Illuminate\Support\Str::endsWith(strtolower($path), ['.mp4','.webm','.mov','.m4v']);
                                $ver = optional($advert->updated_at)->getTimestamp() ?? time();
                                $src = $isExternal ? $path : url('/promo/media/'.$advert->id).'?v='.$ver;
                            @endphp
                            <div class="absolute inset-0 transition-opacity duration-700 ease-in-out opacity-0" data-slide>
                                @isset($advert)
                                    <div class="h-full w-full bg-gradient-to-r from-indigo-900 via-sky-900 to-sky-600 text-white">
                                        <div class="h-full max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 flex flex-col lg:flex-row items-stretch gap-8 lg:gap-10">
                                            <div class="flex-1 flex items-center">
                                                <div class="space-y-4 md:space-y-5 max-w-xl">
                                                    <p class="text-xs md:text-sm uppercase tracking-[0.2em] text-sky-200">Inspiration du moment</p>
                                                    <h3 class="text-2xl md:text-3xl lg:text-4xl font-semibold leading-tight">
                                                        {{ $advert->title }}
                                                    </h3>
                                                    <p class="text-sm md:text-base text-sky-100/90 max-w-lg">
                                                        {{ $advert->description ?? "Des idées, des infos claires, et plus encore. Mettez vos contenus en avant avec une mise en page premium." }}
                                                    </p>
                                                    @if(!empty($advert->link_url))
                                                        <div class="flex flex-wrap gap-3">
                                                            <a href="{{ route('promo.click', $advert->id) }}" class="inline-flex items-center px-5 py-2.5 rounded-full bg-white text-sky-900 text-sm font-semibold shadow-sm hover:bg-sky-50">
                                                                Découvrir maintenant
                                                                <span class="ml-2">→</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center">
                                                <div class="relative w-full max-w-xl aspect-[16/9] rounded-3xl overflow-hidden shadow-2xl ring-1 ring-white/10 bg-sky-900/40">
                                                    @if(!empty($src))
                                                        @if($isVideo)
                                                            <video src="{{ $src }}" class="w-full h-full object-cover" muted playsinline></video>
                                                        @else
                                                            <img src="{{ $src }}" alt="{{ $advert->title }}" class="w-full h-full object-cover" loading="lazy" />
                                                        @endif
                                                    @else
                                                        <div class="w-full h-full grid place-items-center text-white/70">Média indisponible</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endisset
                            </div>
                        @endforeach
                        @if(($ads ?? collect())->isEmpty())
                            <div class="absolute inset-0 grid place-items-center text-neutral-400">Aucune publicité active</div>
                        @endif
                    </div>
                    <div class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2">
                        <button type="button" class="rounded-full bg-black/30 text-white p-2" data-prev>‹</button>
                        <button type="button" class="rounded-full bg-black/30 text-white p-2" data-next>›</button>
                    </div>
                    <div class="absolute bottom-3 left-0 right-0 flex items-center justify-center gap-2" data-dots></div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
                        <div class="h-1 bg-white/80" style="width:0%" data-progress></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            (function(){
                var root = document.getElementById('ad-slideshow-bottom');
                if(!root) return;
                var slides = Array.prototype.slice.call(root.querySelectorAll('[data-slide]'));
                var dots = root.querySelector('[data-dots]');
                var prev = root.querySelector('[data-prev]');
                var next = root.querySelector('[data-next]');
                var progress = root.querySelector('[data-progress]');
                if(!slides.length) return;
                var i = 0;
                var timer = null;
                var intervalAttr = root.getAttribute('data-interval');
                var interval = Math.max(1000, Number(intervalAttr || 3000));
                var tick = null;
                function setActive(idx){
                    slides.forEach(function(s, k){
                        s.style.opacity = (k===idx)? '1' : '0';
                        var v = s.querySelector('video');
                        if(v){
                            if(k===idx){ try{ v.play(); }catch(e){} } else { try{ v.pause(); v.currentTime=0; }catch(e){} }
                        }
                    });
                    var dotEls = Array.prototype.slice.call(dots.children);
                    dotEls.forEach(function(d,k){ d.className = 'h-2 w-2 rounded-full border border-white/70 '+(k===idx?'bg-white':'bg-white/10'); });
                    i = idx;
                    if(progress){ progress.style.width = '0%'; }
                }
                function nextSlide(){ setActive((i+1)%slides.length); }
                function prevSlide(){ setActive((i-1+slides.length)%slides.length); }
                function start(){ stop(); timer = setInterval(nextSlide, interval); if(progress){ var startTs=Date.now(); clearInterval(tick); tick=setInterval(function(){ var p=(Date.now()-startTs)/interval; progress.style.width = Math.min(100, Math.max(0, p*100))+'%'; }, 40); } }
                function stop(){ if(timer){ clearInterval(timer); timer=null; } if(tick){ clearInterval(tick); tick=null; } }
                slides.forEach(function(_,k){ var d=document.createElement('span'); d.className='h-2 w-2 rounded-full border border-white/70 '+(k===0?'bg-white':'bg-white/10'); d.style.cursor='pointer'; d.onclick=function(){ setActive(k); start(); }; dots.appendChild(d); });
                setActive(0);
                prev.onclick=function(){ prevSlide(); start(); };
                next.onclick=function(){ nextSlide(); start(); };
                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);
                start();
            })();
        </script>
    </section>
@endsection
