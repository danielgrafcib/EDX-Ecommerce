<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Models\Advertisement;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CustomerAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\EnterpriseAdminController;
use App\Http\Controllers\Admin\MarketAdminController;
use App\Http\Controllers\Admin\MarketingCampaignAdminController;
use App\Http\Controllers\Admin\AdPlanAdminController;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Enterprise;
use App\Models\Subscription;
use App\Models\AdPlan;
use App\Models\Service;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Front-office client
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $heroProducts = Product::query()->where('is_active', true)->with('images','category')->latest()->take(3)->get();
    $categories = Category::withCount('products')->orderByDesc('products_count')->take(6)->get();
    $promotions = Product::query()->where('is_active', true)->with('images','category')->inRandomOrder()->take(6)->get();
    $bestReviews = [
        ['name' => 'Sarah K.', 'title' => 'Livraison ultra rapide', 'rating' => 5, 'quote' => 'Commande passée le matin, expédiée l’après-midi. Qualité irréprochable !'],
        ['name' => 'Moussa D.', 'title' => 'Service client top', 'rating' => 4, 'quote' => 'Support réactif, ils m’ont aidé à choisir le bon ordinateur.'],
        ['name' => 'Claire B.', 'title' => 'Site moderne et fiable', 'rating' => 5, 'quote' => 'Expérience d’achat fluide, paiement sécurisé, je recommande.'],
    ];
    $siteMetrics = [
        ['label' => 'Clients satisfaits', 'value' => '12k+'],
        ['label' => 'Avis 5★', 'value' => '4.9/5'],
        ['label' => 'Commandes/jour', 'value' => '250+'],
    ];
    $ads = Advertisement::query()->where('is_active', 1)->orderBy('sort_order')->get();
    $adsInterval = (int) (Setting::value('ads_interval_ms', '3000') ?? 3000);

    return view('home', compact('heroProducts', 'categories', 'promotions', 'bestReviews', 'siteMetrics', 'ads', 'adsInterval'));
});

use App\Http\Controllers\BookingController;

Route::get('/services', function () {
    $query = Service::query()->where('is_active', true);
    if (request()->filled('q')) {
        $term = trim(request('q'));
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%');
        });
    }
    if (request()->filled('category')) {
        $query->where('category', 'like', '%'.request('category').'%');
    }
    if (request()->filled('location')) {
        $query->where('location', 'like', '%'.request('location').'%');
    }
    $available = request('available');
    if ($available === '1' || $available === '0') {
        $query->where('is_available', $available === '1');
    }
    if (request()->filled('price_min')) {
        $query->where('price', '>=', request()->float('price_min'));
    }
    if (request()->filled('price_max')) {
        $query->where('price', '<=', request()->float('price_max'));
    }
    if (request()->filled('rating_min')) {
        $query->where('rating', '>=', request()->float('rating_min'));
    }
    $services = $query->orderBy('plan', 'desc')->orderBy('name')->paginate(12)->withQueryString();
    return view('services.index', compact('services'));
});

Route::get('/services/{id}/book', [BookingController::class, 'show'])->name('booking.show');
Route::get('/services/{id}/availability', [BookingController::class, 'availability'])->name('booking.availability');
Route::post('/services/{id}/book', [BookingController::class, 'book'])->name('booking.book');

Route::get('/catalog', function () {
    $query = Product::query()->where('is_active', true)->with('images','category');
    $filters = request()->only(['category_id', 'q', 'price_min', 'price_max', 'sort', 'in_stock']);
    if (request('category_id')) {
        $query->where('category_id', request('category_id'));
    }
    if (request('q')) {
        $query->where('name', 'like', '%'.request('q').'%');
    }
    $sort = request('sort');
    if (request()->filled('price_min')) {
        $query->where('price', '>=', request()->float('price_min'));
    }
    if (request()->filled('price_max')) {
        $query->where('price', '<=', request()->float('price_max'));
    }
    if (request()->boolean('in_stock')) {
        $query->where('stock', '>', 0);
    }
    if ($sort === 'price_asc') {
        $query->orderBy('price', 'asc');
    } elseif ($sort === 'price_desc') {
        $query->orderBy('price', 'desc');
    } elseif ($sort === 'popular') {
        $query->orderBy('stock', 'desc');
    } else {
        $query->latest();
    }
    $perPage = min(max((int) request('per_page', 12), 6), 60);
    $products = $query->paginate($perPage);
    $categories = Category::withCount('products')->orderBy('name')->get();
    $catalogStats = [
        'total' => Product::where('is_active', true)->count(),
        'inStock' => Product::where('is_active', true)->where('stock', '>', 0)->count(),
    ];
    return view('catalog.index', compact('products','categories','filters','catalogStats'));
});

Route::get('/product/{id}', function (int $id) {
    $product = Product::with('category','images')->findOrFail($id);
    $related = Product::where('category_id', $product->category_id)
        ->where('id', '<>', $product->id)
        ->limit(6)->get();
    $inWishlist = false;
    if ($user = request()->user()) {
        $inWishlist = \App\Models\Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->exists();
    }
    return view('product.show', compact('product','related','inWishlist'));
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/search/suggestions', [ProductController::class, 'suggest'])->name('search.suggestions');

Route::get('/promo/media/{id}', function (int $id) {
    $ad = Advertisement::findOrFail($id);
    $path = trim((string)$ad->media_path);
    if ($path === '') {
        abort(404);
    }
    if (preg_match('/^https?:\\/\\//i', $path)) {
        return redirect($path)->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
    $normalized = ltrim(str_replace('\\','/',$path), '/');
    $headers = [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];
    if (str_starts_with($normalized, 'storage/')) {
        $relative = substr($normalized, 8);
        $path = \Illuminate\Support\Facades\Storage::disk('public')->path($relative);
        return response()->file($path, $headers);
    }
    return response()->file(public_path($normalized), $headers);
});

Route::get('/partners', function () {
    $partners = \App\Models\Partner::where('is_active', true)
        ->with(['products.category','articles'])
        ->orderBy('name')
        ->get();
    return view('partners.index', compact('partners'));
});

Route::get('/partners/{slug}/articles', function (string $slug) {
    $partner = \App\Models\Partner::where('slug',$slug)->firstOrFail();
    $articles = \App\Models\PartnerArticle::where('partner_id',$partner->id)
        ->with(['categories','tags'])
        ->latest()->paginate(12);
    return view('partners.articles.index', compact('partner','articles'));
});

Route::get('/partners/{slug}/articles/{id}', function (string $slug, int $id) {
    $partner = \App\Models\Partner::where('slug',$slug)->firstOrFail();
    $article = \App\Models\PartnerArticle::where('partner_id',$partner->id)
        ->with(['images','categories','tags'])
        ->findOrFail($id);
    return view('partners.articles.show', compact('partner','article'));
});

Route::get('/cart', function () {
    $sessionId = request()->session()->getId();
    $userId = optional(request()->user())->id;
    $cart = Cart::query()
        ->where('status', 'active')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
        ->with('items.product')
        ->first();
    if (!$cart) {
        $cart = Cart::create(['user_id' => $userId, 'session_id' => $userId ? null : $sessionId, 'status' => 'active']);
        $cart->load('items.product');
    }
    $subtotal = $cart->items->sum(fn($item) => $item->quantity * $item->unit_price);
    $appliedCoupon = $cart->coupon_code ? \App\Models\Coupon::where('code', $cart->coupon_code)->first() : null;
    $discount = $appliedCoupon ? $appliedCoupon->discountAmount((float)$subtotal) : 0;
    return view('cart.index', compact('cart', 'appliedCoupon', 'discount'));
})
;
Route::post('/cart', [CartController::class, 'add']);
Route::put('/cart/{itemId}', [CartController::class, 'update']);
Route::delete('/cart/{itemId}', [CartController::class, 'remove']);
Route::post('/cart/coupon', [CartController::class, 'applyCoupon']);
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon']);
Route::middleware(['auth','user'])->group(function () {
    Route::post('/wishlist', [WishlistController::class, 'add']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'remove']);
});

Route::get('/checkout', function () {
    $sessionId = request()->session()->getId();
    $userId = optional(request()->user())->id;
    $cart = Cart::query()
        ->where('status', 'active')
        ->when($userId, fn($q) => $q->where('user_id', $userId))
        ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
        ->with('items.product')
        ->first();
    $summarySubtotal = $cart ? $cart->items->sum(fn($item) => $item->quantity * $item->unit_price) : 0;
    $coupon = $cart && $cart->coupon_code ? \App\Models\Coupon::where('code', $cart->coupon_code)->first() : null;
    $discount = $coupon ? $coupon->discountAmount((float)$summarySubtotal) : 0;
    $summary = [
        'subtotal' => $summarySubtotal,
        'discount' => $discount,
        'coupon' => $cart && $cart->coupon_code ? $cart->coupon_code : null,
    ];
    return view('checkout.index', compact('cart', 'summary'));
});
Route::post('/checkout', [CheckoutController::class, 'place']);

Route::get('/account', function () {
    $user = request()->user();
    $orders = $user ? $user->orders()->latest()->with('items.product')->take(5)->get() : collect();
    $addresses = $user ? $user->addresses()->latest()->take(3)->get() : collect();
    $wishlistCount = $user ? \App\Models\Wishlist::where('user_id', $user->id)->count() : 0;
    return view('account.index', compact('user', 'orders', 'addresses', 'wishlistCount'));
})->middleware(['auth','user'])->name('account');

/*
|--------------------------------------------------------------------------
| Back-office admin
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'store']);

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/', function () {
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $customersCount = \App\Models\User::where('is_admin', false)->count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock')->take(5)->get();
        $newCustomers = \App\Models\User::where('is_admin', false)->latest()->take(5)->get();
        $topProducts = \App\Models\OrderItem::selectRaw('product_id, SUM(quantity) as qty, SUM(quantity*unit_price) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->take(5)
            ->get();
        $topProducts->load('product');

        // New dashboard stats
        $enterprisesCount = Enterprise::count();
        $activeAdsCount = Advertisement::where('is_active', true)->count();
        $activeSubscriptionsCount = Subscription::where('status', 'active')->count();
        $activeSubsCount = $activeSubscriptionsCount; // Alias pour la vue
        $adPlans = AdPlan::where('is_active', true)->get();

        return view('admin.dashboard', compact(
            'ordersCount',
            'productsCount',
            'customersCount',
            'totalRevenue',
            'recentOrders',
            'lowStockProducts',
            'newCustomers',
            'topProducts',
            'enterprisesCount',
            'activeAdsCount',
            'activeSubscriptionsCount',
            'activeSubsCount',
            'adPlans'
        ));
    })->name('admin.dashboard');

    // Produits
    Route::get('/products', [ProductAdminController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductAdminController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductAdminController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductAdminController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductAdminController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductAdminController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/{id}/images/{imageId}', [ProductAdminController::class, 'deleteImage'])->name('admin.products.images.destroy');
    Route::put('/products/{id}/images/{imageId}', [ProductAdminController::class, 'updateImage'])->name('admin.products.images.update');
    Route::put('/products/{id}/images/{imageId}/primary', [ProductAdminController::class, 'setPrimary'])->name('admin.products.images.primary');

    // Catégories
    Route::get('/categories', [CategoryAdminController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryAdminController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{id}', [CategoryAdminController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryAdminController::class, 'destroy'])->name('admin.categories.destroy');

    // Clients
    Route::get('/customers', [CustomerAdminController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{id}', [CustomerAdminController::class, 'show'])->name('admin.customers.show');
    Route::put('/customers/{id}/block', [CustomerAdminController::class, 'block'])->name('admin.customers.block');
    Route::put('/customers/{id}/unblock', [CustomerAdminController::class, 'unblock'])->name('admin.customers.unblock');
    Route::delete('/customers/{id}', [CustomerAdminController::class, 'destroy'])->name('admin.customers.destroy');
    Route::post('/customers/{id}/reset-password', [CustomerAdminController::class, 'resetPassword'])->name('admin.customers.reset');

    // Commandes
    Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders.index');
    Route::put('/orders/{id}/status', [OrderAdminController::class, 'updateStatus'])->name('admin.orders.status');
    Route::put('/orders/{id}/tracking', [OrderAdminController::class, 'updateTracking'])->name('admin.orders.tracking');
    Route::get('/orders/{id}/invoice', [OrderAdminController::class, 'invoice'])->name('admin.orders.invoice');

    // Partenariats
    Route::get('/partners', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'index'])->name('admin.partners.index');
    Route::get('/partners/create', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'create'])->name('admin.partners.create');
    Route::post('/partners', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'store'])->name('admin.partners.store');
    Route::get('/partners/{id}/edit', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'edit'])->name('admin.partners.edit');
    Route::put('/partners/{id}', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'update'])->name('admin.partners.update');
    Route::delete('/partners/{id}', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'destroy'])->name('admin.partners.destroy');
    Route::post('/partners/{id}/attach-product', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'attachProduct'])->name('admin.partners.attach');
    Route::delete('/partners/{id}/products/{productId}', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'detachProduct'])->name('admin.partners.detach');
    Route::post('/partners/{id}/images', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'addImage'])->name('admin.partners.images.add');
    Route::delete('/partners/{id}/images/{imageId}', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'deleteImage'])->name('admin.partners.images.delete');
    Route::post('/partners/{id}/images/{imageId}/primary', [\App\Http\Controllers\Admin\PartnerAdminController::class, 'setPrimaryImage'])->name('admin.partners.images.primary');

    Route::get('/partners/{partnerId}/articles', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'index'])->name('admin.partners.articles.index');
    Route::get('/partners/{partnerId}/articles/create', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'create'])->name('admin.partners.articles.create');
    Route::post('/partners/{partnerId}/articles', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'store'])->name('admin.partners.articles.store');
    Route::get('/partners/{partnerId}/articles/{id}/edit', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'edit'])->name('admin.partners.articles.edit');
    Route::put('/partners/{partnerId}/articles/{id}', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'update'])->name('admin.partners.articles.update');
    Route::delete('/partners/{partnerId}/articles/{id}', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'destroy'])->name('admin.partners.articles.destroy');
    Route::post('/partners/{partnerId}/articles/{id}/images', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'addImage'])->name('admin.partners.articles.images.add');
    Route::delete('/partners/{partnerId}/articles/{id}/images/{imageId}', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'deleteImage'])->name('admin.partners.articles.images.delete');
    Route::post('/partners/{partnerId}/articles/{id}/images/{imageId}/primary', [\App\Http\Controllers\Admin\PartnerArticleAdminController::class, 'setPrimaryImage'])->name('admin.partners.articles.images.primary');

    // Codes promo
    Route::get('/coupons', [\App\Http\Controllers\Admin\CouponAdminController::class, 'index'])->name('admin.coupons.index');
    Route::post('/coupons', [\App\Http\Controllers\Admin\CouponAdminController::class, 'store'])->name('admin.coupons.store');
    Route::delete('/coupons/{id}', [\App\Http\Controllers\Admin\CouponAdminController::class, 'destroy'])->name('admin.coupons.destroy');

    // Paramètres
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingAdminController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingAdminController::class, 'update'])->name('admin.settings.update');

    // Publicités
    Route::get('/ads', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'index'])->name('admin.ads.index');
    Route::get('/ads/create', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'create'])->name('admin.ads.create');
    Route::post('/ads', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'store'])->name('admin.ads.store');
    Route::get('/ads/{id}/edit', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'edit'])->name('admin.ads.edit');
    Route::put('/ads/{id}', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'update'])->name('admin.ads.update');
    Route::put('/ads/{id}/media', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'updateMedia'])->name('admin.ads.media.update');
    Route::delete('/ads/{id}', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'destroy'])->name('admin.ads.destroy');
    Route::put('/ads/{id}/toggle', [\App\Http\Controllers\Admin\AdvertisementAdminController::class, 'toggle'])->name('admin.ads.toggle');

    // Entreprises
    Route::get('/enterprises', [EnterpriseAdminController::class, 'index'])->name('admin.enterprises.index');
    Route::get('/enterprises/create', [EnterpriseAdminController::class, 'create'])->name('admin.enterprises.create');
    Route::post('/enterprises', [EnterpriseAdminController::class, 'store'])->name('admin.enterprises.store');
    Route::get('/enterprises/{id}/edit', [EnterpriseAdminController::class, 'edit'])->name('admin.enterprises.edit');
    Route::put('/enterprises/{id}', [EnterpriseAdminController::class, 'update'])->name('admin.enterprises.update');
    Route::delete('/enterprises/{id}', [EnterpriseAdminController::class, 'destroy'])->name('admin.enterprises.destroy');
    Route::post('/enterprises/{id}/attach-product', [EnterpriseAdminController::class, 'attachProduct'])->name('admin.enterprises.attach');
    Route::delete('/enterprises/{id}/products/{productId}', [EnterpriseAdminController::class, 'detachProduct'])->name('admin.enterprises.detach');
    Route::post('/enterprises/{id}/subscribe', [EnterpriseAdminController::class, 'subscribe'])->name('admin.enterprises.subscribe');

    // Marchés
    Route::get('/markets', [MarketAdminController::class, 'index'])->name('admin.markets.index');
    Route::get('/markets/create', [MarketAdminController::class, 'create'])->name('admin.markets.create');
    Route::post('/markets', [MarketAdminController::class, 'store'])->name('admin.markets.store');
    Route::get('/markets/{id}/edit', [MarketAdminController::class, 'edit'])->name('admin.markets.edit');
    Route::put('/markets/{id}', [MarketAdminController::class, 'update'])->name('admin.markets.update');
    Route::delete('/markets/{id}', [MarketAdminController::class, 'destroy'])->name('admin.markets.destroy');

    // Plans de publicité
    Route::get('/ad-plans', [AdPlanAdminController::class, 'index'])->name('admin.ad-plans.index');
    Route::get('/ad-plans/{id}/edit', [AdPlanAdminController::class, 'edit'])->name('admin.ad-plans.edit');
    Route::put('/ad-plans/{id}', [AdPlanAdminController::class, 'update'])->name('admin.ad-plans.update');

    // Campagnes marketing
    Route::get('/marketing-campaigns', [MarketingCampaignAdminController::class, 'index'])->name('admin.marketing-campaigns.index');
    Route::get('/marketing-campaigns/create', [MarketingCampaignAdminController::class, 'create'])->name('admin.marketing-campaigns.create');
    Route::post('/marketing-campaigns', [MarketingCampaignAdminController::class, 'store'])->name('admin.marketing-campaigns.store');
    Route::get('/marketing-campaigns/{id}/edit', [MarketingCampaignAdminController::class, 'edit'])->name('admin.marketing-campaigns.edit');
    Route::put('/marketing-campaigns/{id}', [MarketingCampaignAdminController::class, 'update'])->name('admin.marketing-campaigns.update');
    Route::delete('/marketing-campaigns/{id}', [MarketingCampaignAdminController::class, 'destroy'])->name('admin.marketing-campaigns.destroy');
});

/*
|--------------------------------------------------------------------------
| Routes d'authentification Breeze
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'user'])->name('dashboard');

Route::middleware(['auth','user'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/* Payment Routes */
Route::post('/payment/initiate/{order}', [PaymentController::class, 'initiate'])->name('payment.initiate')->middleware(['auth']);
Route::any('/webhook/{provider}', [PaymentController::class, 'handleWebhook'])->name('webhook.payment')->middleware('signed.webhook');

Route::middleware(['auth'])->prefix('enterprise')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\EnterpriseDashboardController::class, 'index'])->name('enterprise.dashboard');
    Route::get('/{id}/manage', [\App\Http\Controllers\EnterpriseDashboardController::class, 'manage'])->name('enterprise.manage');
});
