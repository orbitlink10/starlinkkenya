@extends('layouts.app', ['title' => 'My Account Checkout | Starlink Kenya'])

@section('content')
    <style>
        .account-page {
            min-height: 100vh;
            background: #f5f7fb;
            color: #142033;
            font-family: Manrope, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding-bottom: 48px;
        }

        .account-wrap {
            width: min(1080px, 94vw);
            margin: 0 auto;
            padding-top: 36px;
        }

        .account-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .account-title {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .account-copy {
            margin: 8px 0 0;
            color: #52627a;
            font-size: 16px;
        }

        .home-link {
            border: 1px solid #cfd8e6;
            border-radius: 999px;
            color: #27364c;
            background: #fff;
            padding: 10px 16px;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
        }

        .flash {
            margin-bottom: 18px;
            border: 1px solid #c9d8ee;
            border-radius: 8px;
            background: #eaf3ff;
            color: #1d4b82;
            padding: 12px 14px;
            font-weight: 700;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 18px;
            align-items: start;
        }

        .panel {
            border: 1px solid #dce3ef;
            border-radius: 8px;
            background: #fff;
            padding: 22px;
        }

        .panel-title {
            margin: 0 0 14px;
            font-size: 20px;
            font-weight: 800;
        }

        .order-meta,
        .order-row,
        .order-total {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            color: #52627a;
            font-size: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #edf1f6;
        }

        .order-row strong,
        .order-total strong {
            color: #152034;
        }

        .order-total {
            border-bottom: 0;
            color: #111927;
            font-size: 18px;
            font-weight: 800;
            padding-bottom: 0;
        }

        .pay-actions {
            display: grid;
            gap: 12px;
        }

        .pay-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            border: 0;
            border-radius: 999px;
            padding: 14px 18px;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            text-decoration: none;
            cursor: pointer;
        }

        .pay-btn.mpesa {
            background: #128c3a;
        }

        .pay-btn.whatsapp {
            background: #a15b0f;
        }

        .pay-note {
            margin: 14px 0 0;
            color: #5f6f87;
            font-size: 14px;
            line-height: 1.55;
        }

        .empty-state {
            color: #52627a;
            line-height: 1.6;
        }

        @media (max-width: 820px) {
            .account-head,
            .checkout-grid {
                grid-template-columns: 1fr;
            }

            .account-head {
                flex-direction: column;
            }
        }
    </style>

    @include('shared.storefront-header')

    <main class="account-page">
        <div class="account-wrap">
            <header class="account-head">
                <div>
                    <h1 class="account-title">My Account</h1>
                    <p class="account-copy">Complete your order using M-Pesa or send it to WhatsApp.</p>
                </div>
                <a class="home-link" href="{{ route('shop.cart.index') }}">Back to Cart</a>
            </header>

            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <section class="checkout-grid">
                <div class="panel">
                    <h2 class="panel-title">Order Summary</h2>

                    @if ($order)
                        <div class="order-meta">
                            <span>Order Number</span>
                            <strong>{{ $order->order_number }}</strong>
                        </div>
                        <div class="order-meta">
                            <span>Status</span>
                            <strong>{{ ucfirst($order->status) }}</strong>
                        </div>
                    @endif

                    @forelse ($items as $item)
                        <div class="order-row">
                            <span>{{ $item['name'] }} x{{ (int) $item['quantity'] }}</span>
                            <strong>KSh {{ number_format((float) $item['line_total'], 2) }}</strong>
                        </div>
                    @empty
                        <p class="empty-state">Your cart is empty. Add a product before completing checkout.</p>
                    @endforelse

                    <div class="order-total">
                        <span>Total</span>
                        <strong>KSh {{ number_format($total, 2) }}</strong>
                    </div>
                </div>

                <aside class="panel">
                    <h2 class="panel-title">Payment Options</h2>

                    <div class="pay-actions">
                        <a class="pay-btn mpesa" href="{{ $mpesaDialUrl }}">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                            Pay with M-Pesa
                        </a>

                        <a class="pay-btn whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
                            <i class="fa-brands fa-whatsapp"></i>
                            Order via WhatsApp
                        </a>
                    </div>

                    <p class="pay-note">
                        Use the order total and order number when paying or confirming through WhatsApp.
                    </p>
                </aside>
            </section>
        </div>
    </main>
@endsection
