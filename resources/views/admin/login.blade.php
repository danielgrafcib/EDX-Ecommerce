<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion Admin – {{ config('app.name') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-neutral-50 font-sans antialiased">
    <main class="max-w-sm mx-auto px-4 py-20">
        <h1 class="text-2xl font-semibold text-neutral-50 mb-6">Connexion administrateur</h1>
        <form method="post" action="/admin/login" class="space-y-4 rounded-2xl border border-neutral-800 bg-neutral-900 p-6">
            @csrf
            <label class="text-xs font-medium text-neutral-300 block">
                Email
                <input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
            </label>
            <label class="text-xs font-medium text-neutral-300 block">
                Mot de passe
                <input name="password" type="password" class="mt-1 w-full rounded-lg border border-neutral-700 bg-neutral-950 px-3 py-2 text-sm text-neutral-50" required>
            </label>
            @error('email')
            <div class="text-xs text-red-300">{{ $message }}</div>
            @enderror
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-xs text-neutral-300">
                    <input type="checkbox" name="remember" class="rounded border-neutral-600 bg-neutral-950 text-sky-500">
                    Se souvenir de moi
                </label>
                <button class="px-4 py-2 rounded-xl bg-sky-600 text-sm font-semibold text-white">Se connecter</button>
            </div>
        </form>
    </main>
</body>
</html>
