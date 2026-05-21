@extends('layouts.app', ['title' => $product->name.' | Starlink Kenya Installers'])

@section('content')
    <style>
        .shop-page {
            min-height: 100vh;
            padding: 42px 0 56px;
        }

        .container {
            width: min(1180px, 92vw);
            margin: 0 auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 26px;
        }

        .back-link,
        .cart-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.92);
            color: var(--ink);
            font-size: 15px;
            font-weight: 600;
            padding: 12px 18px;
            box-shadow: 0 10px 24px rgba(14, 37, 79, 0.05);
        }

        .flash {
            margin-bottom: 12px;
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid #bfdcc8;
            background: #eefaf2;
            color: #21633b;
            font-weight: 600;
        }

        .product-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 40px;
            border: 1px solid var(--border);
            border-radius: 34px;
            background: linear-gradient(180deg, rgba(238, 246, 255, 0.7) 0%, #fff 100%);
            padding: 30px;
            box-shadow: var(--shadow);
        }

        .product-image {
            width: 100%;
            height: 100%;
            min-height: 360px;
            object-fit: cover;
            border-radius: 28px;
            border: 1px solid var(--border);
            background: var(--panel);
        }

        .product-name {
            margin: 0;
            font-size: clamp(30px, 3vw, 42px);
            line-height: 1.08;
            color: var(--ink);
            letter-spacing: -0.035em;
        }

        .meta {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            border: 1px solid rgba(255, 145, 28, .18);
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
        }

        .description {
            margin-top: 18px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.68;
        }

        .product-details {
            margin-top: 30px;
            border: 1px solid var(--border);
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.95);
            padding: 34px 34px 38px;
            box-shadow: 0 22px 46px rgba(14, 37, 79, 0.06);
        }

        .product-details h2 {
            margin: 0 0 22px;
            color: var(--ink);
            font-size: clamp(28px, 2.4vw, 38px);
            line-height: 1.1;
            letter-spacing: -0.035em;
        }

        .product-copy {
            color: var(--muted);
        }

        .product-copy > *:first-child {
            margin-top: 0;
        }

        .product-copy > *:last-child {
            margin-bottom: 0;
        }

        .product-copy p,
        .product-copy ul,
        .product-copy ol {
            margin: 0;
            font-size: 17px;
            line-height: 1.8;
        }

        .product-copy h2,
        .product-copy h3,
        .product-copy h4 {
            margin: 34px 0 14px;
            color: var(--ink);
            line-height: 1.18;
            letter-spacing: -0.03em;
        }

        .product-copy h2 {
            font-size: clamp(28px, 2.1vw, 36px);
        }

        .product-copy h3 {
            font-size: clamp(24px, 1.8vw, 30px);
        }

        .product-copy h4 {
            font-size: clamp(20px, 1.55vw, 24px);
        }

        .product-copy ul,
        .product-copy ol {
            padding-left: 24px;
        }

        .product-copy li + li {
            margin-top: 8px;
        }

        .product-copy a {
            color: #0b4b8e;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .product-copy blockquote {
            margin: 26px 0 0;
            border-left: 4px solid #ff961f;
            padding: 4px 0 4px 18px;
            color: #27425f;
            font-size: 18px;
            font-weight: 600;
            line-height: 1.7;
        }

        .product-copy img {
            display: block;
            width: 100%;
            max-width: 920px;
            height: auto;
            margin-top: 26px;
            border-radius: 24px;
        }

        .product-copy table {
            width: 100%;
            margin-top: 26px;
            border-collapse: collapse;
            border: 1px solid #e2eaf4;
            background: #fff;
        }

        .product-copy th,
        .product-copy td {
            border: 1px solid #e2eaf4;
            padding: 12px 14px;
            text-align: left;
            font-size: 15px;
        }

        .product-copy th {
            background: #f7faff;
            color: #133764;
            font-weight: 800;
        }

        .product-copy * + p,
        .product-copy * + ul,
        .product-copy * + ol,
        .product-copy * + table,
        .product-copy * + blockquote {
            margin-top: 22px;
        }

        .price-row {
            margin-top: 24px;
            display: flex;
            align-items: baseline;
            gap: 14px;
            flex-wrap: wrap;
        }

        .price {
            margin: 0;
            font-size: clamp(32px, 2.55vw, 40px);
            color: var(--brand-dark);
            letter-spacing: -0.04em;
        }

        .marked-price {
            margin: 0;
            font-size: 24px;
            color: #8c7f73;
            text-decoration: line-through;
        }

        .actions {
            margin-top: 28px;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .actions form {
            display: inline-flex;
        }

        .product-action-btn {
            border: none;
            border-radius: 999px;
            padding: 15px 24px;
            font-size: 16px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 14px 28px rgba(14, 37, 79, 0.08);
        }

        .product-action-btn:hover {
            transform: translateY(-1px);
        }

        .btn-whatsapp {
            background: #21b463;
            color: #fff;
        }

        .btn-cart {
            min-width: 186px;
            justify-content: center;
            background: linear-gradient(135deg, #f58d19 0%, #ffae42 100%);
            color: #fff;
            box-shadow: 0 12px 24px rgba(245, 141, 25, .22);
        }

        .qty-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .qty-wrap input {
            width: 108px;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px 14px;
            font-size: 16px;
            font-weight: 600;
            min-height: 54px;
            color: var(--ink);
            background: rgba(255, 255, 255, 0.92);
        }

        .stock {
            margin-top: 18px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .product-shell {
                grid-template-columns: 1fr;
            }

            .product-name {
                font-size: 28px;
            }

            .price {
                font-size: 30px;
            }

            .product-details {
                padding: 26px 22px 30px;
            }
        }
    </style>

    <main class="shop-page">
        <div class="container">
            @include('shared.quick-links-nav')

            <div class="top-bar">
                <a class="back-link" href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
                <a class="cart-link" href="{{ route('shop.cart.index') }}"><i class="fa-solid fa-cart-shopping"></i> Cart ({{ $cartCount }})</a>
            </div>

            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <section class="product-shell">
                <div>
                    <img class="product-image" src="{{ $imageUrl }}" alt="{{ $product->name }}">
                </div>

                <div>
                    <h1 class="product-name">{{ $product->name }}</h1>

                    <div class="meta">
                        @if ($product->category)
                            <span class="meta-chip">{{ $product->category->name }}</span>
                        @endif
                        @if ($product->subCategory)
                            <span class="meta-chip">{{ $product->subCategory->name }}</span>
                        @endif
                    </div>

                    <p class="description">{{ $productSummary }}</p>

                    <div class="price-row">
                        <p class="price">KES {{ number_format((float) $product->price, 2) }}</p>
                        @if (!is_null($product->marked_price) && (float) $product->marked_price > (float) $product->price)
                            <p class="marked-price">KES {{ number_format((float) $product->marked_price, 2) }}</p>
                        @endif
                    </div>

                    <div class="stock">
                        @if ((int) ($product->quantity ?? $product->stock ?? 0) > 0)
                            In stock: {{ (int) ($product->quantity ?? $product->stock ?? 0) }}
                        @else
                            Available on request
                        @endif
                    </div>

                    <div class="actions">
                        <a class="product-action-btn btn-whatsapp" href="{{ route('shop.product.whatsapp', $product) }}" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp"></i> Order on WhatsApp
                        </a>

                        <form method="POST" action="{{ route('shop.cart.add', $product) }}">
                            @csrf
                            <div class="qty-wrap">
                                <input type="number" name="quantity" min="1" max="99" value="1" aria-label="Quantity">
                                <button class="product-action-btn btn-cart" type="submit"><i class="fa-solid fa-cart-plus"></i> Add to Cart</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            @if ($productDetailsHtml)
                <section class="product-details">
                    <h2>Product Details</h2>
                    <div class="product-copy">
                        {!! $productDetailsHtml !!}
                    </div>
                </section>
            @endif
        </div>
    </main>
@endsection
