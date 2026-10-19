@php
    $searchValue = $searchValue ?? '';
    $rootBaseUrl = request()->getSchemeAndHttpHost();
    $loginEntryUrl = $rootBaseUrl.'/login.php';
    $accountUrl = auth()->check() ? route('account.dashboard') : $loginEntryUrl;
    $phoneNumber = (string) config('seo.phone', '+254701299299');
    $phoneHref = 'tel:'.preg_replace('/\D+/', '', $phoneNumber);
    $whatsappPhone = (string) config('seo.whatsapp_phone', '254700123456');
    $whatsappHref = 'https://wa.me/'.preg_replace('/\D+/', '', $whatsappPhone);
    $siteLogoPath = trim((string) config('seo.logo_path', ''), '/');
    $siteLogoUrl = $siteLogoPath !== '' ? route('media.show', ['path' => $siteLogoPath]) : null;
    $cartCount = (int) collect(session('cart', []))
        ->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
    $siteMenu = collect($siteNavigationMenu ?? \App\Models\HomepageContent::currentNavigationMenu())
        ->map(function (array $item) use ($rootBaseUrl): array {
            $href = trim((string) ($item['href'] ?? ''));

            if ($href !== '' && str_starts_with($href, '#')) {
                $href = $rootBaseUrl.'/'.$href;
            }

            return [
                'label' => trim((string) ($item['label'] ?? '')),
                'href' => $href,
            ];
        })
        ->filter(fn (array $item): bool => $item['label'] !== '' && $item['href'] !== '')
        ->values()
        ->all();
    $audienceTabs = [
        ['label' => 'Personal', 'href' => $rootBaseUrl.'/#packages', 'active' => true],
        ['label' => 'Business', 'href' => $rootBaseUrl.'/#installation', 'active' => false],
    ];
@endphp

@once
    <style>
        .storefront-header {
            position: relative;
            z-index: 20;
            overflow: hidden;
            background:
                radial-gradient(circle at 12% 22%, rgba(255, 255, 255, 0.82) 0 1px, transparent 1.5px),
                radial-gradient(circle at 32% 70%, rgba(255, 255, 255, 0.6) 0 1px, transparent 1.5px),
                radial-gradient(circle at 56% 36%, rgba(255, 255, 255, 0.7) 0 1px, transparent 1.5px),
                radial-gradient(circle at 78% 68%, rgba(255, 255, 255, 0.56) 0 1px, transparent 1.5px),
                linear-gradient(180deg, #202733 0%, #121821 100%);
            background-size: 180px 74px, 220px 94px, 260px 86px, 190px 90px, auto;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 16px 34px rgba(7, 14, 24, 0.18);
        }

        .storefront-header::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.12) 48%, rgba(255, 255, 255, 0.02)),
                radial-gradient(circle at 70% 0%, rgba(255, 255, 255, 0.14), transparent 34%);
        }

        .storefront-header__inner {
            position: relative;
            z-index: 1;
            width: min(1400px, 94vw);
            min-height: 94px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: clamp(22px, 3vw, 56px);
            padding: 0;
        }

        .storefront-brand {
            display: inline-flex;
            align-items: center;
            flex: 0 0 auto;
        }

        .storefront-brand__mark {
            display: none;
        }

        .storefront-brand__logo {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            object-fit: contain;
            background: #fff;
            padding: 8px;
        }

        .storefront-brand__mark--logo::after {
            display: none;
        }

        .storefront-brand__text {
            line-height: 1;
        }

        .storefront-brand__text strong {
            display: block;
            font-size: clamp(24px, 2vw, 32px);
            font-weight: 800;
            color: #fff;
            letter-spacing: .24em;
            text-shadow: 0 1px 18px rgba(255, 255, 255, 0.2);
        }

        .storefront-brand__text small {
            display: none;
        }

        .storefront-nav {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .storefront-nav a {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
            transition: color .2s ease;
        }

        .storefront-nav a:hover {
            color: #fff;
        }

        .storefront-spacer {
            flex: 1 1 auto;
            min-width: 12px;
        }

        .storefront-call {
            display: flex;
            align-items: baseline;
            gap: 6px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 18px;
            font-weight: 500;
            white-space: nowrap;
        }

        .storefront-call strong {
            color: #fff;
            font-weight: 900;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .storefront-audience {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px;
            border-radius: 8px;
            background: rgba(113, 122, 137, 0.88);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .storefront-audience a {
            min-width: 126px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            padding: 14px 20px;
            color: #fff;
            font-size: 18px;
            font-weight: 900;
            line-height: 1;
            transition: background .2s ease;
        }

        .storefront-audience a.is-active,
        .storefront-audience a:hover {
            background: rgba(173, 181, 195, 0.74);
        }

        .storefront-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .storefront-icon {
            position: relative;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: rgba(255, 255, 255, 0.82);
            font-size: 22px;
            border: 0;
            background: rgba(113, 122, 137, 0.68);
            box-shadow: none;
            transition: background .2s ease, color .2s ease;
        }

        .storefront-icon:hover {
            color: #fff;
            background: rgba(137, 146, 162, 0.86);
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
            width: 56px;
        }

        @media (max-width: 980px) {
            .storefront-header__inner {
                flex-wrap: wrap;
                gap: 12px 18px;
                padding: 16px 0;
            }

            .storefront-brand {
                min-width: 0;
            }

            .storefront-nav {
                order: 5;
                width: 100%;
                overflow-x: auto;
                gap: 22px;
                scrollbar-width: none;
            }

            .storefront-nav::-webkit-scrollbar {
                display: none;
            }

            .storefront-actions {
                margin-left: 0;
            }
        }

        @media (max-width: 760px) {
            .storefront-header {
                position: sticky;
                top: 0;
                z-index: 30;
                box-shadow: 0 8px 18px rgba(7, 14, 24, 0.22);
            }

            .storefront-header__inner {
                width: min(1400px, calc(100vw - 24px));
                min-height: auto;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                padding: 12px 0;
            }

            .storefront-brand {
                margin-right: auto;
            }

            .storefront-brand__text strong {
                font-size: 19px;
                letter-spacing: .18em;
            }

            .storefront-spacer,
            .storefront-call {
                display: none;
            }

            .storefront-audience {
                order: 2;
                gap: 4px;
                padding: 3px;
            }

            .storefront-audience a {
                min-width: 84px;
                padding: 10px 12px;
                font-size: 13px;
            }

            .storefront-nav {
                order: 4;
                flex-wrap: nowrap;
                overflow-x: auto;
                width: calc(100% + 24px);
                margin: 2px -12px 0;
                padding: 2px 12px;
                gap: 18px;
                scrollbar-width: none;
            }

            .storefront-nav::-webkit-scrollbar {
                display: none;
            }

            .storefront-nav a {
                min-height: 30px;
                white-space: nowrap;
                font-size: 14px;
            }

            .storefront-actions {
                order: 3;
                gap: 7px;
            }

            .storefront-icon,
            .storefront-icon--chevron {
                width: 42px;
                height: 42px;
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
                font-size: 16px;
            }
        }
    </style>
@endonce

<header class="storefront-header">
    <div class="storefront-header__inner">
        <a class="storefront-brand" href="{{ route('home') }}" aria-label="Starlink Kenya home">
            <span class="storefront-brand__mark {{ $siteLogoUrl ? 'storefront-brand__mark--logo' : '' }}">
                @if ($siteLogoUrl)
                    <img class="storefront-brand__logo" src="{{ $siteLogoUrl }}" alt="Starlink Kenya logo">
                @else
                    <i class="fa-solid fa-satellite-dish"></i>
                @endif
            </span>
            <span class="storefront-brand__text">
                <strong>STARLINK</strong>
                <small>KENYA INSTALLERS</small>
            </span>
        </a>

        <nav class="storefront-nav" aria-label="Primary navigation">
            @foreach ($siteMenu as $menuItem)
                <a href="{{ $menuItem['href'] }}">{{ $menuItem['label'] }}</a>
            @endforeach
        </nav>

        <div class="storefront-spacer" aria-hidden="true"></div>

        <a class="storefront-call" href="{{ $phoneHref }}" aria-label="Call {{ $phoneNumber }}">
            <span>Call</span>
            <strong>{{ $phoneNumber }}</strong>
        </a>

        <nav class="storefront-audience" aria-label="Audience navigation">
            @foreach ($audienceTabs as $tab)
                <a class="{{ $tab['active'] ? 'is-active' : '' }}" href="{{ $tab['href'] }}">{{ $tab['label'] }}</a>
            @endforeach
        </nav>

        <div class="storefront-actions">
            <a class="storefront-icon storefront-icon--chevron" href="{{ $accountUrl }}" aria-label="Account menu">
                <i class="fa-solid fa-bars"></i>
            </a>
        </div>
    </div>
</header>
