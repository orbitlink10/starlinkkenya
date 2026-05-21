@extends('layouts.app', ['title' => 'Shopping Cart | Starlink Kenya Installers'])

@section('content')
    <style>
        .cart-page {
            min-height: 100vh;
            padding: 48px 0 60px;
            background: #f4f7fb;
        }

        .cart-container {
            width: min(1600px, 90vw);
            margin: 0 auto;
        }

        .cart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 58px;
        }

        .cart-title {
            margin: 0;
            color: #051833;
            font-size: clamp(30px, 2.4vw, 40px);
            line-height: 1.08;
            font-weight: 800;
            letter-spacing: 0;
        }

        .cart-copy {
            margin: 12px 0 0;
            color: #52627a;
            font-size: 17px;
            line-height: 1.35;
        }

        .continue-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d9dee8;
            border-radius: 999px;
            background: #fff;
            color: #07162d;
            padding: 12px 22px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 10px 28px rgba(16, 35, 62, 0.04);
        }

        .flash {
            margin: 0 0 20px;
            border: 1px solid #bfdcc8;
            border-radius: 14px;
            background: #eefaf2;
            color: #21633b;
            padding: 12px 14px;
            font-weight: 700;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(360px, 480px);
            gap: 36px;
            align-items: start;
        }

        .cart-items {
            display: grid;
            gap: 24px;
        }

        .cart-item,
        .summary-card {
            border: 1px solid #dce2eb;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 22px 50px rgba(17, 34, 61, 0.08);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 120px minmax(240px, 1fr) auto auto;
            gap: 24px;
            align-items: center;
            min-height: 170px;
            padding: 24px;
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #dce2eb;
            border-radius: 16px;
            background: #f3f6fb;
        }

        .cart-item-name {
            margin: 0;
            color: #051833;
            font-size: 19px;
            line-height: 1.22;
            font-weight: 800;
            letter-spacing: 0;
        }

        .cart-item-price {
            margin: 12px 0 0;
            color: #5b6c86;
            font-size: 16px;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qty-form {
            display: inline-flex;
        }

        .qty-btn,
        .qty-value,
        .remove-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dce2eb;
            background: #fff;
            color: #061733;
        }

        .qty-btn {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            font-size: 22px;
            font-weight: 900;
            line-height: 1;
            cursor: pointer;
        }

        .qty-value {
            width: 108px;
            height: 56px;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 500;
        }

        .cart-item-total {
            min-width: 190px;
            color: #03142c;
            font-size: 18px;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .remove-btn {
            width: 54px;
            height: 54px;
            border: 0;
            border-radius: 14px;
            background: #e8314a;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        .summary-card {
            padding: 28px 30px;
        }

        .summary-title {
            margin: 0 0 26px;
            color: #111927;
            font-size: 28px;
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: 0;
        }

        .summary-line,
        .summary-total {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            color: #55627a;
            font-size: 18px;
            line-height: 1.35;
        }

        .summary-line + .summary-line {
            margin-top: 20px;
        }

        .summary-divider {
            height: 1px;
            margin: 26px 0;
            background: #d9dee8;
        }

        .summary-total {
            color: #111927;
            font-weight: 900;
        }

        .checkout-btn {
            width: min(100%, 420px);
            display: flex;
            justify-content: center;
            margin-inline: auto;
            margin-top: 26px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff8a1f 0%, #ffb144 100%);
            color: #fff;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
        }

        .checkout-btn:disabled {
            cursor: not-allowed;
            opacity: 0.55;
        }

        .summary-note {
            margin: 20px 0 0;
            color: #8fa0bb;
            text-align: center;
            font-size: 16px;
            line-height: 1.35;
        }

        .empty-card {
            border: 1px solid #dce2eb;
            border-radius: 24px;
            background: #fff;
            padding: 30px;
            color: #52627a;
            font-size: 16px;
        }

        .checkout-modal {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: none;
            align-items: flex-start;
            padding: 42px 4vw;
            background: rgba(4, 10, 18, 0.58);
            overflow-y: auto;
        }

        .checkout-modal.is-open {
            display: flex;
        }

        .modal-panel {
            width: min(750px, 94vw);
            border: 1px solid #d3d8e1;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.22);
            overflow: hidden;
        }

        .modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            border-bottom: 1px solid #d9dee7;
            padding: 26px 24px;
        }

        .modal-title {
            margin: 0;
            color: #111927;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0;
        }

        .modal-close {
            border: 0;
            background: transparent;
            color: #54585f;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        .modal-body {
            padding: 26px 24px 30px;
            text-align: center;
        }

        .signin-copy {
            margin: 0;
            color: #202837;
            font-size: 17px;
        }

        .signin-link {
            display: inline-flex;
            margin-top: 28px;
            color: #0050a8;
            font-size: 17px;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .checkout-form {
            display: grid;
            gap: 24px;
            margin-top: 28px;
        }

        .checkout-field {
            width: 100%;
            border: 1px solid #cfd6e1;
            border-radius: 7px;
            padding: 14px 18px;
            color: #172033;
            font-size: 16px;
            outline: none;
        }

        .register-btn {
            width: 100%;
            border: 0;
            border-radius: 999px;
            background: #a15b0f;
            color: #fff;
            padding: 16px 20px;
            font-size: 17px;
            font-weight: 800;
            cursor: pointer;
        }

        @media (max-width: 1100px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 100px minmax(0, 1fr);
            }

            .qty-controls,
            .cart-item-total,
            .remove-form {
                grid-column: 2;
            }

            .cart-item-total {
                text-align: left;
            }
        }

        @media (max-width: 640px) {
            .cart-header {
                align-items: flex-start;
                flex-direction: column;
                margin-bottom: 28px;
            }

            .continue-btn,
            .cart-copy,
            .summary-line,
            .summary-total,
            .summary-note,
            .checkout-btn {
                font-size: 18px;
            }

            .cart-item {
                grid-template-columns: 1fr;
            }

            .cart-item-image,
            .qty-controls,
            .cart-item-total,
            .remove-form {
                grid-column: 1;
            }

            .cart-item-image {
                width: 100%;
                height: 180px;
            }
        }
    </style>

    <main class="cart-page">
        <div class="cart-container">
            <header class="cart-header">
                <div>
                    <h1 class="cart-title">Shopping Cart</h1>
                    <p class="cart-copy">Review your items and proceed to checkout when ready.</p>
                </div>

                <a class="continue-btn" href="{{ route('home') }}">Continue Shopping</a>
            </header>

            @if (session('success'))
                <div class="flash">{{ session('success') }}</div>
            @endif

            <section class="cart-layout">
                <div class="cart-items">
                    @forelse ($items as $item)
                        @php
                            $quantity = (int) $item['quantity'];
                            $productId = (int) $item['product_id'];
                        @endphp

                        <article class="cart-item">
                            <img class="cart-item-image" src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">

                            <div>
                                <h2 class="cart-item-name">{{ $item['name'] }}</h2>
                                <p class="cart-item-price">KSh {{ number_format((float) $item['price'], 2) }} each</p>
                            </div>

                            <div class="qty-controls" aria-label="Quantity controls for {{ $item['name'] }}">
                                <form class="qty-form" method="POST" action="{{ route('shop.cart.update', ['product' => $productId]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ max(1, $quantity - 1) }}">
                                    <button class="qty-btn" type="submit" aria-label="Decrease quantity" @disabled($quantity <= 1)>-</button>
                                </form>

                                <span class="qty-value">{{ $quantity }}</span>

                                <form class="qty-form" method="POST" action="{{ route('shop.cart.update', ['product' => $productId]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ min(99, $quantity + 1) }}">
                                    <button class="qty-btn" type="submit" aria-label="Increase quantity">+</button>
                                </form>
                            </div>

                            <div class="cart-item-total">KSh {{ number_format((float) $item['line_total'], 2) }}</div>

                            <form class="remove-form" method="POST" action="{{ route('shop.cart.remove', ['product' => $productId]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="remove-btn" type="submit" aria-label="Remove {{ $item['name'] }}">&times;</button>
                            </form>
                        </article>
                    @empty
                        <div class="empty-card">Your cart is empty. Open a product and click Add to Cart to start ordering.</div>
                    @endforelse
                </div>

                <aside class="summary-card">
                    <h2 class="summary-title">Order Summary</h2>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>KSh {{ number_format($total, 2) }}</span>
                    </div>
                    <div class="summary-line">
                        <span>Shipping</span>
                        <span>KSh 0.00</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-total">
                        <span>Total</span>
                        <span>KSh {{ number_format($total, 2) }}</span>
                    </div>

                    <button class="checkout-btn" type="button" data-open-checkout @disabled($items->isEmpty())>Proceed to Checkout</button>

                    <p class="summary-note">Secure checkout and quick delivery options available.</p>
                </aside>
            </section>
        </div>
    </main>

    <div class="checkout-modal" data-checkout-modal aria-hidden="true">
        <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="checkout-modal-title">
            <div class="modal-head">
                <h2 class="modal-title" id="checkout-modal-title">Create an Account</h2>
                <button class="modal-close" type="button" data-close-checkout aria-label="Close checkout">&times;</button>
            </div>

            <div class="modal-body">
                <p class="signin-copy">Already have an account?</p>
                <a class="signin-link" href="{{ route('login') }}">Sign In Here</a>

                <form class="checkout-form" method="GET" action="{{ $checkoutWhatsappUrl }}" target="_blank">
                    <input class="checkout-field" name="first_name" type="text" placeholder="First Name" autocomplete="given-name" required>
                    <input class="checkout-field" name="last_name" type="text" placeholder="Last Name" autocomplete="family-name" required>
                    <input class="checkout-field" name="phone" type="tel" placeholder="Phone Number" autocomplete="tel" required>
                    <input class="checkout-field" name="email" type="email" placeholder="Email" autocomplete="email" required>
                    <input class="checkout-field" name="password" type="password" placeholder="Password" autocomplete="new-password" required>
                    <input class="checkout-field" name="password_confirmation" type="password" placeholder="Confirm Password" autocomplete="new-password" required>

                    <button class="register-btn" type="submit">Register</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.querySelector('[data-checkout-modal]');
            const openButton = document.querySelector('[data-open-checkout]');
            const closeButton = document.querySelector('[data-close-checkout]');

            if (!modal || !openButton || !closeButton) {
                return;
            }

            const openModal = () => {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                modal.querySelector('input')?.focus();
            };

            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                openButton.focus();
            };

            openButton.addEventListener('click', openModal);
            closeButton.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
