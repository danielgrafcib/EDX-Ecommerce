<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <aside class="hidden md:flex md:flex-col w-64 border-r border-neutral-800 bg-neutral-950">
            <div class="px-5 py-4 border-b border-neutral-800 flex items-center justify-between">
                <a href="/admin" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-neutral-50">
                    <x-edex-logo class="h-6 w-6" />
                    <span>{{ config('app.name', 'Boutique Pro') }}</span>
                    <span class="ml-1 text-xs font-normal text-sky-400">Admin</span>
                </a>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="/admin" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>📊</span><span>Dashboard</span>
                </a>
                @php($lowCount = \App\Models\Product::where('stock','<=',5)->count())
                <a href="/admin/products" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/products*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>🛒</span><span>Produits</span>
                    @if($lowCount > 0)
                        <span class="ml-auto rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] text-amber-300">{{ $lowCount }}</span>
                    @endif
                </a>
                <a href="/admin/partners" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/partners*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>🤝</span><span>Partenaires</span>
                </a>
                <a href="/admin/categories" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/categories*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>🏷️</span><span>Catégories</span>
                </a>
                <a href="/admin/ads" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/ads*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>📣</span><span>Publicités</span>
                </a>
                <a href="/admin/customers" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/customers*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>👤</span><span>Clients</span>
                </a>
                <a href="/admin/orders" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/orders*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>📦</span><span>Commandes</span>
                </a>
                <a href="/admin/coupons" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/coupons*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>🏷️</span><span>Codes promo</span>
                </a>
                <a href="/admin/settings" class="flex items-center gap-2 px-3 py-2 rounded-xl {{ request()->is('admin/settings*') ? 'bg-neutral-800 text-white' : 'text-neutral-300 hover:bg-neutral-900' }}">
                    <span>⚙️</span><span>Paramètres</span>
                </a>
            </nav>
            <div class="px-5 py-4 border-t border-neutral-800 text-xs text-neutral-500">
                Connecté en tant que
                <span class="font-semibold text-neutral-200">{{ auth()->user()->name ?? 'Admin' }}</span>
            </div>
        </aside>
        <main class="flex-1 bg-neutral-900 text-neutral-50">
            <header class="md:hidden px-4 py-3 border-b border-neutral-800 flex items-center justify-between bg-neutral-950">
                <span class="font-semibold text-neutral-50">{{ config('app.name', 'Boutique Pro') }} – Admin</span>
                <a href="/" class="text-xs text-neutral-400 underline">Voir le site</a>
            </header>
            <div class="max-w-6xl mx-auto px-4 py-6">
                @yield('content')
            </div>
            @php($lowCount = \App\Models\Product::where('stock','<=',5)->count())
            @if($lowCount > 0)
                <footer class="border-t border-neutral-800 bg-neutral-950">
                    <div class="max-w-6xl mx-auto px-4 py-3 text-xs flex items-center justify-between">
                        <div class="text-amber-300">Alertes stock: {{ $lowCount }} produit(s) proche de la rupture</div>
                        <a href="/admin/products" class="px-3 py-1 rounded-lg border border-amber-500/40 text-amber-200">Voir et agir</a>
                    </div>
                </footer>
            @endif
        </main>
    </div>
    @stack('scripts')
</body>
</html>












