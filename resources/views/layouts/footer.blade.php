<footer class="bg-neutral-900 text-neutral-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 py-12 grid gap-8 md:grid-cols-4">
        <section>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Liens rapides</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="/" class="hover:text-white">Accueil</a></li>
                <li><a href="/catalog" class="hover:text-white">Boutique</a></li>
                <li><a href="/account" class="hover:text-white">Mon compte</a></li>
                <li><a href="/cart" class="hover:text-white">Panier</a></li>
                <li><a href="/admin/login" class="hover:text-white">Admin</a></li>
            </ul>
        </section>
        <section>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Catégories</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @php($footerCategories = \App\Models\Category::orderBy('name')->take(8)->get())
                @foreach($footerCategories as $c)
                    <li><a href="/catalog?category_id={{ $c->id }}" class="hover:text-white">{{ $c->name }}</a></li>
                @endforeach
            </ul>
        </section>
        <section>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Informations légales</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="#" class="hover:text-white">CGV</a></li>
                <li><a href="#" class="hover:text-white">Politique de confidentialité</a></li>
                <li><a href="#" class="hover:text-white">Mentions légales</a></li>
                <li><a href="#" class="hover:text-white">Retours et remboursements</a></li>
            </ul>
        </section>
        <section>
            <h3 class="text-sm font-semibold uppercase tracking-wide text-neutral-400">Contact & newsletter</h3>
            <p class="mt-3 text-sm text-neutral-400">Email: support@example.com<br>Tél: +33 1 23 45 67 89</p>
            <div class="mt-4">
                <form method="post" action="#" class="flex gap-2">
                    <input type="email" name="newsletter_email" placeholder="Votre email" class="flex-1 rounded-lg border border-neutral-700 bg-neutral-800 px-3 py-2 text-sm text-neutral-100" />
                    <button class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white">S’inscrire</button>
                </form>
            </div>
            <div class="mt-4 flex items-center gap-3 text-lg">
                <a href="#" aria-label="Facebook">📘</a>
                <a href="#" aria-label="Instagram">📸</a>
                <a href="#" aria-label="Twitter">🐦</a>
                <a href="#" aria-label="YouTube">▶️</a>
            </div>
            <div class="mt-4 text-sm text-neutral-400">Moyens de paiement: Visa, MasterCard, PayPal</div>
        </section>
    </div>
    <div class="border-t border-neutral-800">
        <div class="max-w-7xl mx-auto px-4 py-6 text-xs text-neutral-400">
            © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
        </div>
    </div>
</footer>
