<nav x-data="{ q: '', suggestions: [], loading: false }" class="bg-white border-b border-neutral-200">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex h-16 items-center justify-between gap-4">
            <a href="/" class="flex items-center gap-2 font-semibold text-neutral-900">
                <x-edex-logo class="h-8 w-8" />
                <span>{{ config('app.name', 'Boutique Pro') }}</span>
            </a>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="inline-flex items-center gap-2 text-sm text-neutral-700 hover:text-neutral-900">
                        Catégories
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"/></svg>
                    </button>
                    <div x-cloak x-show="open" @click.outside="open=false" class="absolute left-0 mt-2 w-64 rounded-xl border border-neutral-200 bg-white shadow-lg z-20" style="display: none;">
                        <ul class="py-2 text-sm">
                            @php($navCategories = \App\Models\Category::orderBy('name')->take(8)->get())
                            @foreach($navCategories as $c)
                                <li>
                                    <a href="/catalog?category_id={{ $c->id }}" class="block px-3 py-2 hover:bg-neutral-50">{{ $c->name }}</a>
                                </li>
                            @endforeach
                            <li class="border-t">
                                <a href="/catalog" class="block px-3 py-2 text-neutral-600">Toutes les catégories →</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <a href="/catalog" class="text-sm text-neutral-700 hover:text-neutral-900">Boutique</a>
                <a href="/partners" class="text-sm text-neutral-700 hover:text-neutral-900">Partenariats</a>
                <a href="/account" class="text-sm text-neutral-700 hover:text-neutral-900">Mon compte</a>
                <a href="/cart" class="inline-flex items-center gap-2 rounded-full bg-neutral-900 px-4 py-2 text-sm font-semibold text-white">Panier</a>
            </div>
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input x-model="q" @input.debounce.300ms="loading=true;fetch('/search/suggestions?q='+encodeURIComponent(q)).then(r=>r.json()).then(d=>{suggestions=d;}).finally(()=>loading=false)" type="search" placeholder="Rechercher..." class="w-full rounded-full border border-neutral-300 px-3 py-1.5 text-sm" />
                    <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 animate-spin border-2 border-neutral-300 border-t-sky-600 rounded-full"></div>
                    <div x-show="suggestions.length && q.length >= 2" class="absolute left-0 right-0 mt-2 rounded-xl border border-neutral-200 bg-white shadow-lg z-30">
                        <ul class="py-2 text-sm">
                            <template x-for="s in suggestions" :key="s.id">
                                <li>
                                    <a :href="'/product/' + s.id" class="flex items-center justify-between px-3 py-2 hover:bg-neutral-50">
                                        <span x-text="s.name"></span>
                                        <span class="text-neutral-500" x-text="new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(s.price)"></span>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                        @csrf
                        <button class="text-sm text-neutral-700 hover:text-neutral-900">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-neutral-700 hover:text-neutral-900">Connexion</a>
                    <a href="{{ route('register') }}" class="hidden lg:inline-flex items-center rounded-full border border-neutral-300 px-3 py-1.5 text-sm text-neutral-700 hover:border-sky-600">Créer un compte</a>
                @endauth
                
            </div>
        </div>
        
    </div>
</nav>
