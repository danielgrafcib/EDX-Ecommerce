<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-white text-neutral-900">
    <main class="max-w-3xl mx-auto p-6">
        <header class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold">Facture</h1>
                <p class="text-sm text-neutral-600">Commande #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }} — {{ $order->created_at?->format('d/m/Y') }}</p>
            </div>
            <div class="text-right">
                <p class="font-semibold">{{ config('app.name') }}</p>
                <p class="text-sm text-neutral-600">support@example.com</p>
            </div>
        </header>
        <section class="grid gap-6 sm:grid-cols-2 mb-6">
            <div class="rounded-xl border border-neutral-200 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Client</h2>
                <p class="mt-2 font-semibold">{{ $order->user?->name ?? 'Client' }}</p>
                <p class="text-sm text-neutral-600">{{ $order->user?->email }}</p>
            </div>
            <div class="rounded-xl border border-neutral-200 p-4">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-500">Résumé</h2>
                <p class="mt-2 text-neutral-700">Statut: <strong>{{ ucfirst($order->status) }}</strong></p>
                <p class="text-neutral-700">Total: <strong>{{ number_format($order->total, 2, ',', ' ') }} €</strong></p>
            </div>
        </section>
        <div class="overflow-x-auto rounded-xl border border-neutral-200">
            <table class="w-full text-sm">
                <thead class="border-b">
                    <tr>
                        <th class="p-3 text-left">Produit</th>
                        <th class="p-3 text-right">Qté</th>
                        <th class="p-3 text-right">Prix unitaire</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b last:border-0">
                            <td class="p-3">{{ $item->product?->name ?? 'Produit' }}</td>
                            <td class="p-3 text-right">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ number_format($item->unit_price, 2, ',', ' ') }} €</td>
                            <td class="p-3 text-right">{{ number_format($item->quantity * $item->unit_price, 2, ',', ' ') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 text-right">
            <a href="#" onclick="window.print();return false;" class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white">Imprimer</a>
        </div>
    </main>
</body>
</html>

