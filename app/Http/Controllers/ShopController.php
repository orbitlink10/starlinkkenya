<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AnalyticsService;
use App\Support\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function show(Request $request, string $productSlug, AnalyticsService $analyticsService): View|RedirectResponse
    {
        $product = Product::query()
            ->where('slug', $productSlug)
            ->when(
                ctype_digit($productSlug),
                fn ($query) => $query->orWhere('id', (int) $productSlug)
            )
            ->firstOrFail();

        if ($product->slug && $productSlug !== $product->slug) {
            return redirect()->route('shop.product.show', ['productSlug' => $product->slug]);
        }

        abort_unless($product->is_active, 404);

        $imageUrl = $product->image_path
            ? route('media.show', ['path' => $product->image_path])
            : 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1200&q=80';

        [
            'summary' => $productSummary,
            'detailsHtml' => $productDetailsHtml,
        ] = $this->productCopy($product);

        $analyticsService->trackPageView($request, $product->name, 'product', [
            'product_id' => $product->id,
            'slug' => $product->slug,
            'price' => (float) $product->price,
        ]);

        return view('shop.product', [
            'product' => $product,
            'imageUrl' => $imageUrl,
            'cartCount' => $this->cartCount($request),
            'productSummary' => $productSummary,
            'productDetailsHtml' => $productDetailsHtml,
            'seo' => [
                'title' => $product->name.' | Starlink Kenya',
                'description' => $productSummary,
                'canonical' => route('shop.product.show', ['productSlug' => $product->slug ?: $product->id]),
                'type' => 'product',
                'image' => $imageUrl,
                'schema' => [
                    SeoData::breadcrumbSchema([
                        ['name' => 'Home', 'url' => route('home')],
                        ['name' => 'Starlink Kits in Kenya', 'url' => route('home').'#packages'],
                        ['name' => $product->name, 'url' => route('shop.product.show', ['productSlug' => $product->slug ?: $product->id])],
                    ]),
                    SeoData::productSchema(
                        $product,
                        route('shop.product.show', ['productSlug' => $product->slug ?: $product->id]),
                        SeoData::trimDescription($productSummary),
                        $imageUrl,
                    ),
                ],
            ],
        ]);
    }

    public function addToCart(Request $request, Product $product, AnalyticsService $analyticsService): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $quantityToAdd = (int) ($validated['quantity'] ?? 1);
        $stockLimit = (int) ($product->quantity ?? $product->stock ?? 0);

        $cart = $request->session()->get('cart', []);
        $itemKey = (string) $product->id;
        $existingQuantity = (int) ($cart[$itemKey]['quantity'] ?? 0);
        $newQuantity = $existingQuantity + $quantityToAdd;

        if ($stockLimit > 0) {
            $newQuantity = min($newQuantity, $stockLimit);
        }

        $cart[$itemKey] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->price,
            'quantity' => $newQuantity,
            'image_path' => $product->image_path,
        ];

        $request->session()->put('cart', $cart);

        $analyticsService->trackEvent($request, 'add_to_cart', $product->name, 'product', [
            'product_id' => $product->id,
            'slug' => $product->slug,
            'quantity' => $quantityToAdd,
            'cart_quantity' => $newQuantity,
        ], '/product/'.($product->slug ?: $product->id));

        return redirect()
            ->route('shop.cart.index')
            ->with('success', $product->name.' added to cart.');
    }

    public function cart(Request $request, AnalyticsService $analyticsService): View
    {
        $cart = $request->session()->get('cart', []);
        $items = collect(array_values($cart))->map(function (array $item): array {
            $item['line_total'] = (float) $item['price'] * (int) $item['quantity'];
            $item['image_url'] = !empty($item['image_path'])
                ? route('media.show', ['path' => $item['image_path']])
                : 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=720&q=80';

            return $item;
        });

        $total = (float) $items->sum('line_total');

        $analyticsService->trackPageView($request, 'Shopping Cart', 'cart', [
            'items_count' => $items->count(),
            'cart_count' => $this->cartCount($request),
            'total' => $total,
        ]);

        return view('shop.cart', [
            'items' => $items,
            'total' => $total,
            'cartCount' => $this->cartCount($request),
            'checkoutWhatsappUrl' => route('shop.cart.whatsapp'),
            'seo' => [
                'title' => 'Your Cart | Starlink Kenya',
                'description' => 'Review your selected Starlink kits and accessories before checkout.',
                'canonical' => route('shop.cart.index'),
                'robots' => 'noindex,follow',
            ],
        ]);
    }

    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[(string) $product->id]);
        $request->session()->put('cart', $cart);

        return redirect()
            ->route('shop.cart.index')
            ->with('success', $product->name.' removed from cart.');
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $request->session()->get('cart', []);
        $itemKey = (string) $product->id;

        if (! isset($cart[$itemKey])) {
            return redirect()->route('shop.cart.index');
        }

        $stockLimit = (int) ($product->quantity ?? $product->stock ?? 0);
        $quantity = (int) $validated['quantity'];

        if ($stockLimit > 0) {
            $quantity = min($quantity, $stockLimit);
        }

        $cart[$itemKey]['quantity'] = $quantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('shop.cart.index');
    }

    public function redirectToProductWhatsapp(Product $product, Request $request, AnalyticsService $analyticsService): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $analyticsService->trackEvent($request, 'whatsapp_product_click', $product->name, 'product', [
            'product_id' => $product->id,
            'slug' => $product->slug,
            'price' => (float) $product->price,
        ], '/product/'.($product->slug ?: $product->id));

        return redirect()->away($this->whatsappUrlForProduct($product));
    }

    public function redirectToCartWhatsapp(Request $request, AnalyticsService $analyticsService): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        $items = collect(array_values($cart))->map(function (array $item): array {
            $item['line_total'] = (float) $item['price'] * (int) $item['quantity'];

            return $item;
        });

        $total = (float) $items->sum('line_total');

        $analyticsService->trackEvent($request, 'whatsapp_cart_click', 'Cart Checkout', 'cart', [
            'items_count' => $items->count(),
            'cart_count' => $this->cartCount($request),
            'total' => $total,
        ], '/cart');

        return redirect()->away($this->whatsappUrlForCart($items, $total));
    }

    private function cartCount(Request $request): int
    {
        $cart = $request->session()->get('cart', []);

        return (int) collect($cart)->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
    }

    private function whatsappUrlForProduct(Product $product): string
    {
        $message = sprintf(
            'Hello, I want to order %s at KES %s.',
            $product->name,
            number_format((float) $product->price, 2)
        );

        return 'https://wa.me/'.$this->whatsappPhone().'?text='.urlencode($message);
    }

    private function whatsappUrlForCart($items, float $total): string
    {
        if ($items->isEmpty()) {
            return 'https://wa.me/'.$this->whatsappPhone();
        }

        $lines = $items->map(
            fn (array $item): string => sprintf(
                '- %s x%d (KES %s)',
                $item['name'],
                (int) $item['quantity'],
                number_format((float) $item['line_total'], 2)
            )
        )->implode("\n");

        $message = "Hello, I want to order:\n".$lines."\nTotal: KES ".number_format($total, 2);

        return 'https://wa.me/'.$this->whatsappPhone().'?text='.urlencode($message);
    }

    /**
     * @return array{summary:string, detailsHtml:?string}
     */
    private function productCopy(Product $product): array
    {
        $defaultSummary = 'Reliable Starlink hardware and accessories for seamless connectivity.';
        $rawDescription = trim((string) $product->description);
        $rawMetaDescription = trim((string) $product->meta_description);

        if ($rawDescription === '') {
            $summary = $rawMetaDescription !== ''
                ? $this->plainText($rawMetaDescription)
                : $defaultSummary;

            return [
                'summary' => $summary !== '' ? $summary : $defaultSummary,
                'detailsHtml' => null,
            ];
        }

        $decodedDescription = html_entity_decode($rawDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $descriptionText = $this->plainText($decodedDescription);
        $hasHtmlMarkup = $decodedDescription !== strip_tags($decodedDescription);
        $shouldShowDetails = $hasHtmlMarkup || mb_strlen($descriptionText) > 260;

        $summarySource = $rawMetaDescription !== ''
            ? $this->plainText($rawMetaDescription)
            : ($shouldShowDetails ? Str::limit($descriptionText, 220) : $descriptionText);

        return [
            'summary' => $summarySource !== '' ? $summarySource : $defaultSummary,
            'detailsHtml' => $shouldShowDetails ? $decodedDescription : null,
        ];
    }

    private function plainText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) ?? '');
    }

    private function whatsappPhone(): string
    {
        return preg_replace('/\D+/', '', (string) config('seo.whatsapp_phone', '254700123456')) ?: '254700123456';
    }
}
