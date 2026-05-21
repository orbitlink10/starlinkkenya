@php
    $searchValue = $searchValue ?? '';
    $rootBaseUrl = request()->getSchemeAndHttpHost();
    $loginEntryUrl = $rootBaseUrl.'/login.php';
    $dashboardEntryUrl = $rootBaseUrl.'/dashboard.php';
    $accountUrl = auth()->check() ? $dashboardEntryUrl : $loginEntryUrl;
    $phoneNumber = (string) config('seo.phone', '+254701299299');
    $phoneHref = 'tel:'.preg_replace('/\D+/', '', $phoneNumber);
    $whatsappPhone = (string) config('seo.whatsapp_phone', '254700123456');
    $whatsappHref = 'https://wa.me/'.preg_replace('/\D+/', '', $whatsappPhone);
    $cartCount = (int) collect(session('cart', []))
        ->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
@endphp

@once
    <style>
        .storefront-header {
            background: rgba(248, 251, 255, 0.78);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(217, 230, 244, 0.92);
        }

        .storefront-header__inner {
            width: min(1400px, 94vw);
            min-height: 104px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 28px;
            padding: 18px 0 20px;
        }

        .storefront-brand {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            min-width: 296px;
        }

        .storefront-brand__mark {
            position: relative;
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #112447 0%, #1f3a6d 100%);
            color: #fff;
            font-size: 28px;
            box-shadow: 0 16px 30px rgba(17, 36, 71, 0.24);
        }

        .storefront-brand__mark::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            right: 10px;
            top: 10px;
            border-radius: 50%;
            background: #ff9b2f;
            box-shadow: 0 0 0 6px rgba(255, 155, 47, 0.12);
        }

        .storefront-brand__text {
            line-height: 1;
        }

        .storefront-brand__text strong {
            display: block;
            font-size: clamp(24px, 1.9vw, 34px);
            font-weight: 800;
            color: #0d1c3d;
            letter-spacing: .02em;
        }

        .storefront-brand__text small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #586f91;
            letter-spacing: .24em;
        }

        .storefront-search {
            width: 100%;
            display: flex;
            align-items: stretch;
            border: 1px solid #d9e6f4;
            border-radius: 26px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 20px 54px rgba(15, 37, 79, 0.08);
        }

        .storefront-search input {
            flex: 1;
            border: 0;
            background: transparent;
            color: #0d1c3d;
            height: 72px;
            padding: 0 28px;
            font-size: 17px;
            font-family: inherit;
            outline: none;
        }

        .storefront-search input::placeholder {
            color: #586f91;
        }

        .storefront-search button {
            border: 0;
            min-width: 78px;
            padding: 0 24px;
            background: linear-gradient(135deg, #ff9b2f 0%, #ffb14f 100%);
            color: #fff;
            font-size: 18px;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: -10px 0 24px rgba(255, 155, 47, 0.2);
        }

        .storefront-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .storefront-icon {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            color: #0d1c3d;
            font-size: 20px;
            border: 1px solid #d9e6f4;
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 10px 24px rgba(14, 37, 79, 0.05);
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease;
        }

        .storefront-icon:hover {
            transform: translateY(-1px);
            color: #eb8111;
            border-color: rgba(255, 155, 47, 0.24);
            box-shadow: 0 14px 28px rgba(14, 37, 79, 0.08);
        }

        .storefront-cart-badge {
            position: absolute;
            top: -7px;
            right: -7px;
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            border: 2px solid #fff;
            background: #ff9b2f;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: grid;
            place-items: center;
            padding: 0 6px;
        }

        .storefront-icon--chevron {
            width: 48px;
        }

        @media (max-width: 980px) {
            .storefront-header__inner {
                grid-template-columns: minmax(0, 1fr) auto;
                justify-items: stretch;
                gap: 18px;
            }

            .storefront-brand {
                min-width: 0;
                justify-self: start;
            }

            .storefront-search {
                grid-column: 1 / -1;
                grid-row: 2;
            }

            .storefront-actions {
                justify-self: end;
            }
        }

        @media (max-width: 760px) {
            .storefront-header {
                position: sticky;
                top: 0;
                z-index: 30;
                box-shadow: 0 8px 18px rgba(14, 37, 79, 0.06);
            }

            .storefront-header__inner {
                width: min(1400px, calc(100vw - 24px));
                min-height: auto;
                gap: 9px 10px;
                padding: 8px 0 10px;
            }

            .storefront-brand {
                gap: 10px;
            }

            .storefront-brand__mark {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                font-size: 20px;
            }

            .storefront-brand__text strong {
                font-size: 19px;
            }

            .storefront-brand__text small {
                margin-top: 4px;
                font-size: 8px;
                letter-spacing: .14em;
            }

            .storefront-search {
                grid-column: 1 / -1;
                grid-row: 2;
                border-radius: 18px;
                box-shadow: 0 10px 22px rgba(14, 37, 79, 0.06);
            }

            .storefront-search input {
                height: 50px;
                padding: 0 14px;
                font-size: 14px;
            }

            .storefront-search button {
                min-width: 52px;
                padding: 0 14px;
            }

            .storefront-actions {
                gap: 7px;
                justify-self: end;
            }

            .storefront-icon,
            .storefront-icon--chevron {
                width: 40px;
                height: 40px;
                border-radius: 14px;
                font-size: 17px;
            }
        }

        @media (max-width: 420px) {
            .storefront-brand__text strong {
                font-size: 18px;
            }

            .storefront-brand__text small {
                font-size: 7px;
            }

            .storefront-icon,
            .storefront-icon--chevron {
                width: 38px;
                height: 38px;
                border-radius: 13px;
                font-size: 16px;
            }
        }
    </style>
@endonce

<header class="storefront-header">
    <div class="storefront-header__inner">
        <a class="storefront-brand" href="{{ route('home') }}" aria-label="Starlink Kenya Installers home">
            <span class="storefront-brand__mark"><i class="fa-solid fa-satellite-dish"></i></span>
            <span class="storefront-brand__text">
                <strong>STARLINK</strong>
                <small>KENYA INSTALLERS</small>
            </span>
        </a>

        <form class="storefront-search" action="{{ route('home') }}#packages" method="GET" role="search">
            <input type="search" name="q" value="{{ $searchValue }}" placeholder="Search Starlink kits, mounts, and accessories" aria-label="Search Starlink products">
            <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <div class="storefront-actions">
            <a class="storefront-icon" href="{{ $whatsappHref }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
            <a class="storefront-icon" href="{{ route('shop.cart.index') }}" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="storefront-cart-badge">{{ $cartCount }}</span>
            </a>
            <a class="storefront-icon" href="{{ $phoneHref }}" aria-label="Call {{ $phoneNumber }}">
                <i class="fa-solid fa-phone"></i>
            </a>
            <a class="storefront-icon storefront-icon--chevron" href="{{ $accountUrl }}" aria-label="Account menu">
                <i class="fa-solid fa-chevron-down"></i>
            </a>
        </div>
    </div>
</header>
