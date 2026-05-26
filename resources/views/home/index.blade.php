@extends('layouts.app', ['title' => 'Starlink Kenya | Official Reseller and Installer in Kenya'])

@section('content')
    @php
        $heroTitle = $homepageContent?->hero_header_title ?: 'Starlink Kenya | Official Starlink Reseller and Installer in Kenya';
        $heroDescription = $homepageContent?->hero_header_description ?: 'Starlink Kenya: Get expert Starlink installation services designed to deliver the strongest signal and maximum performance, even in remote and hard-to-reach areas.';
        $whyChooseTitle = $homepageContent?->why_choose_title ?: 'Why Starlink Kenya Is Ideal for You';
        $whyChooseDescription = $homepageContent?->why_choose_description ?: 'Tailored for the Kenyan market with expert installation and dependable support.';
        $productsTitle = $homepageContent?->products_section_title ?: 'Starlink Kits in Kenya';
        $homePageContentHtml = $homePageContentHtml ?? '<h2>Starlink Kenya: A Comprehensive Guide to Satellite Internet Connectivity</h2><p>Explore STARLINK KENYA, the satellite internet service transforming digital access across Kenya.</p>';
        $heroImageUrl = $homepageContent?->hero_image_path ? route('media.show', ['path' => $homepageContent->hero_image_path]) : null;
        $coverageVideoEmbedUrl = $homepageContent?->youtubeEmbedUrl() ?: \App\Models\HomepageContent::defaultYoutubeEmbedUrl();
        $primaryPhone = (string) config('seo.phone', '+254701299299');
        $primaryPhoneHref = 'tel:'.preg_replace('/\D+/', '', $primaryPhone);
        $whatsappHref = 'https://wa.me/'.preg_replace('/\D+/', '', (string) config('seo.whatsapp_phone', '254700123456'));
        $productFallbackImage = asset('images/product-placeholder.svg');

        $packageHighlights = [
            ['title' => 'Residential Lite', 'desc' => 'Entry-level Starlink connectivity from KES 1,300/month for light browsing, field teams, and remote sites.', 'icon' => 'fa-signal', 'cta' => 'See pricing', 'href' => '#prices'],
            ['title' => 'Residential Unlimited', 'desc' => 'The core Starlink Kenya plan for homes and small offices that need stable all-day connectivity.', 'icon' => 'fa-house-signal', 'cta' => 'Compare kits', 'href' => '#packages'],
            ['title' => 'Business & Installation', 'desc' => 'Higher-performance hardware, dish placement, and rollout support for schools, lodges, farms, and offices.', 'icon' => 'fa-building', 'cta' => 'Talk to us', 'href' => $primaryPhoneHref],
        ];

        $serviceCards = [
            ['title' => 'Buy Starlink Kits in Kenya', 'desc' => 'Compare genuine Starlink kits, mounts, and accessories for homes, offices, camps, and rural installations.', 'href' => '#packages', 'cta' => 'View kits'],
            ['title' => 'Starlink Installation Services in Kenya', 'desc' => 'Certified installers offering dish alignment, mounting, cable runs, and activation support across Kenya.', 'href' => '#installation', 'cta' => 'Explore setup'],
            ['title' => 'Starlink Prices and Packages', 'desc' => 'Review kit pricing, monthly plans, and the differences between Lite, Residential, and business-ready setups.', 'href' => '#prices', 'cta' => 'See pricing'],
            ['title' => 'Network Expansion and Wi-Fi Coverage', 'desc' => 'Extend Starlink into offices, compounds, lodges, and farms with practical Wi-Fi and network rollout support.', 'href' => $primaryPhoneHref, 'cta' => 'Request advice'],
        ];

        $orderSteps = [
            'Choose the right Starlink kit and monthly package for your home, office, lodge, farm, or remote site.',
            'Confirm availability, delivery timeline, and installation requirements with our team by call or WhatsApp.',
            'Book dispatch or professional installation anywhere in Kenya and get setup support after delivery.',
        ];

        $cartCount = (int) collect(session('cart', []))
            ->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));

        $rootBaseUrl = request()->getSchemeAndHttpHost();
        $loginEntryUrl = $rootBaseUrl.'/login.php';
        $accountEntryUrl = auth()->check() ? route('account.dashboard') : $loginEntryUrl;
        $homeNavigationMenu = collect($siteNavigationMenu ?? \App\Models\HomepageContent::defaultNavigationMenu())
            ->map(function (array $item): array {
                $href = trim((string) ($item['href'] ?? ''));

                return [
                    'label' => trim((string) ($item['label'] ?? '')),
                    'href' => $href,
                ];
            })
            ->filter(fn (array $item): bool => $item['label'] !== '' && $item['href'] !== '')
            ->values()
            ->all();
        $caseStudies = collect($caseStudies ?? []);
        $caseStudiesHeadline = "Where We've Delivered Connectivity: Trusted by Organizations Nationwide.";
        $caseStudiesIntro = 'Browse a few of the installations, support rollouts, and connectivity deployments our team has delivered across Kenya.';
    @endphp

    <style>
        :root {
            --page-bg: #f4f7fc;
            --surface: #ffffff;
            --surface-soft: #eef6ff;
            --surface-soft-2: #f8fbff;
            --ink-900: #0d1c3d;
            --ink-700: #243b64;
            --ink-500: #586f91;
            --line: #d9e6f4;
            --line-strong: #c7dbee;
            --orange: #ff9b2f;
            --orange-dark: #eb8111;
            --orange-soft: #fff2df;
            --sky: #d9ecff;
            --sky-strong: #8bd9ff;
            --shadow-soft: 0 20px 54px rgba(15, 37, 79, 0.08);
            --shadow-card: 0 14px 32px rgba(14, 37, 79, 0.06);
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(188, 222, 255, 0.44) 0%, transparent 24%),
                linear-gradient(180deg, #f8fbff 0%, var(--page-bg) 100%);
            font-family: 'Manrope', sans-serif;
        }

        .home {
            color: var(--ink-900);
            overflow-x: clip;
            position: relative;
            isolation: isolate;
        }

        .home::before,
        .home::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .home::before {
            width: 540px;
            height: 540px;
            top: -190px;
            right: -180px;
            background: radial-gradient(circle, rgba(203, 231, 255, 0.72) 0%, rgba(203, 231, 255, 0) 70%);
        }

        .home::after {
            width: 400px;
            height: 400px;
            top: 320px;
            left: -220px;
            background: radial-gradient(circle, rgba(255, 185, 104, 0.18) 0%, rgba(255, 185, 104, 0) 72%);
        }

        .container {
            width: min(1320px, 92vw);
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .store-header {
            background: rgba(248, 251, 255, 0.78);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(217, 230, 244, 0.92);
        }

        .notice-bar,
        .main-bar {
            background: transparent;
        }

        .notice-inner {
            min-height: 72px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            padding: 14px 0 10px;
        }

        .notice-left {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 22px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.78);
            color: var(--ink-700);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            box-shadow: 0 10px 24px rgba(14, 37, 79, 0.04);
        }

        .notice-right {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--ink-700);
            font-size: 15px;
        }

        .notice-right a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.8);
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
            box-shadow: 0 10px 24px rgba(14, 37, 79, 0.04);
        }

        .notice-right a:hover {
            transform: translateY(-1px);
            border-color: var(--line-strong);
            box-shadow: 0 14px 28px rgba(14, 37, 79, 0.08);
        }

        .main-bar-inner {
            min-height: 104px;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 28px;
            padding: 8px 0 24px;
        }

        .brand-block {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            min-width: 296px;
        }

        .brand-mark {
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

        .brand-mark::after {
            content: '';
            position: absolute;
            width: 12px;
            height: 12px;
            right: 10px;
            top: 10px;
            border-radius: 50%;
            background: var(--orange);
            box-shadow: 0 0 0 6px rgba(255, 155, 47, 0.12);
        }

        .brand-text {
            line-height: 1;
        }

        .brand-text strong {
            display: block;
            font-size: clamp(24px, 1.9vw, 34px);
            font-weight: 800;
            color: var(--ink-900);
            letter-spacing: .02em;
        }

        .brand-text small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-500);
            letter-spacing: .24em;
        }

        .header-search {
            width: 100%;
            display: flex;
            align-items: stretch;
            border: 1px solid var(--line);
            border-radius: 26px;
            overflow: hidden;
            background: var(--surface);
            box-shadow: var(--shadow-soft);
        }

        .header-search input {
            flex: 1;
            border: 0;
            background: transparent;
            color: var(--ink-900);
            height: 72px;
            padding: 0 28px;
            font-size: 17px;
            font-family: inherit;
            outline: none;
        }

        .header-search input::placeholder {
            color: var(--ink-500);
        }

        .header-search button {
            border: 0;
            min-width: 78px;
            padding: 0 24px;
            background: linear-gradient(135deg, var(--orange) 0%, #ffb14f 100%);
            color: #fff;
            font-size: 18px;
            display: grid;
            place-items: center;
            cursor: pointer;
            box-shadow: -10px 0 24px rgba(255, 155, 47, 0.2);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            color: var(--ink-900);
            font-size: 20px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.86);
            box-shadow: 0 10px 24px rgba(14, 37, 79, 0.05);
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease;
        }

        .header-icon:hover {
            transform: translateY(-1px);
            color: var(--orange-dark);
            border-color: rgba(255, 155, 47, 0.24);
            box-shadow: 0 14px 28px rgba(14, 37, 79, 0.08);
        }

        .cart-badge {
            position: absolute;
            top: -7px;
            right: -7px;
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            border: 2px solid var(--surface);
            background: var(--orange);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: grid;
            place-items: center;
            padding: 0 6px;
        }

        .chevron-icon {
            width: 48px;
        }

        .section-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin: 20px 0 28px;
            padding: 12px;
            border-radius: 24px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.82);
            box-shadow: 0 14px 28px rgba(14, 37, 79, 0.04);
        }

        .section-nav a {
            border-radius: 18px;
            padding: 12px 18px;
            color: var(--ink-700);
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .01em;
            transition: .2s ease;
        }

        .section-nav a:hover {
            background: var(--orange-soft);
            color: var(--orange-dark);
            transform: translateY(-1px);
        }

        .mobile-shop-rail {
            display: none;
        }

        .section-anchor {
            display: block;
            height: 0;
            scroll-margin-top: 24px;
        }

        .btn {
            border-radius: 999px;
            padding: 16px 28px;
            border: 1px solid transparent;
            font-size: 17px;
            font-weight: 700;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover,
        .view-btn:hover,
        .apply-btn:hover,
        .field-submit:hover {
            transform: translateY(-2px);
        }

        .btn-outline {
            border-color: var(--line);
            color: var(--ink-900);
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 12px 24px rgba(14, 37, 79, 0.04);
        }

        .btn-outline:hover {
            border-color: rgba(255, 155, 47, 0.26);
            background: var(--surface);
        }

        .btn-orange,
        .view-btn,
        .field-submit {
            background: linear-gradient(135deg, var(--orange) 0%, #ffb14f 100%);
            color: #fff;
            box-shadow: 0 16px 32px rgba(255, 155, 47, 0.24);
        }

        .hero-welcome-badge,
        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 11px 22px;
            background: rgba(230, 236, 244, 0.92);
            border: 1px solid rgba(196, 208, 224, 0.8);
            color: var(--ink-700);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .hero {
            padding: 28px 0 72px;
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(0, 1.08fr);
            align-items: center;
            gap: 52px;
        }

        .hero h1 {
            margin: 18px 0 0;
            font-size: clamp(40px, 4.2vw, 58px);
            line-height: 1;
            letter-spacing: -.045em;
            font-weight: 800;
            max-width: 11ch;
        }

        .hero h1 strong {
            color: var(--orange-dark);
        }

        .hero p {
            margin: 20px 0 0;
            max-width: 30ch;
            font-size: clamp(17px, 1.15vw, 20px);
            color: var(--ink-500);
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            gap: 18px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .hero-banner {
            min-height: 520px;
            position: relative;
            overflow: hidden;
            border-radius: 40px;
            border: 1px solid rgba(31, 58, 109, 0.12);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow:
                18px 18px 0 #22384c,
                0 38px 80px rgba(14, 37, 79, 0.14);
        }

        .hero-banner::before,
        .hero-banner::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-banner::before {
            width: 220px;
            height: 220px;
            top: -70px;
            left: -70px;
            background: radial-gradient(circle, rgba(255, 155, 47, 0.3) 0%, rgba(255, 155, 47, 0) 72%);
        }

        .hero-banner::after {
            width: 320px;
            height: 320px;
            top: -76px;
            right: -144px;
            background: radial-gradient(circle, rgba(140, 217, 255, 0.34) 0%, rgba(140, 217, 255, 0) 70%);
        }

        .hero-banner.has-image {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .hero-banner-image {
            position: absolute;
            inset: 24px;
            width: calc(100% - 48px);
            height: calc(100% - 48px);
            object-fit: contain;
            border-radius: 28px;
            background: #fff;
            z-index: 1;
        }

        .dish-shape {
            position: absolute;
            inset: 56px 52px;
            border-radius: 32px;
            border: 1px solid rgba(31, 58, 109, 0.12);
            background:
                radial-gradient(circle at 18% 78%, rgba(140, 217, 255, 0.26) 0 56px, transparent 57px),
                radial-gradient(circle at 78% 24%, rgba(255, 155, 47, 0.12) 0 62px, transparent 63px),
                linear-gradient(135deg, #ffffff 0%, #f2f8ff 100%);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.78);
        }

        .dish-shape::before {
            content: '';
            position: absolute;
            left: 12%;
            top: 22%;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--ink-900);
            box-shadow:
                180px 104px 0 var(--ink-900),
                432px 88px 0 var(--ink-900),
                548px 204px 0 var(--ink-900);
        }

        .dish-shape::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(32deg, transparent 35.5%, rgba(13, 28, 61, 0.3) 36%, rgba(13, 28, 61, 0.3) 36.6%, transparent 37%) 0 0 / 100% 100% no-repeat,
                linear-gradient(-24deg, transparent 50.2%, rgba(13, 28, 61, 0.3) 50.6%, rgba(13, 28, 61, 0.3) 51%, transparent 51.4%) 0 0 / 100% 100% no-repeat,
                linear-gradient(24deg, transparent 56.5%, rgba(13, 28, 61, 0.3) 56.9%, rgba(13, 28, 61, 0.3) 57.3%, transparent 57.7%) 0 0 / 100% 100% no-repeat;
            opacity: .9;
        }

        .section {
            padding: 72px 0;
        }

        .section-title {
            margin: 0;
            font-size: clamp(28px, 2.3vw, 40px);
            line-height: 1.12;
            letter-spacing: -.035em;
        }

        .section-intro {
            margin: 18px 0 0;
            font-size: clamp(16px, 1vw, 18px);
            color: var(--ink-500);
            max-width: 62ch;
            line-height: 1.62;
        }

        .case-studies-section {
            padding-top: 6px;
        }

        .case-studies-head {
            display: grid;
            gap: 16px;
            margin-bottom: 30px;
        }

        .case-studies-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--orange-dark);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .34em;
            text-transform: uppercase;
        }

        .case-studies-kicker::before {
            content: '';
            width: 42px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--orange) 0%, rgba(255, 155, 47, 0) 100%);
        }

        .case-studies-grid {
            display: grid;
            gap: 22px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .case-study-card {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 18px;
            border-radius: 28px;
            border: 1px solid var(--line);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(245, 249, 255, 0.96) 100%);
            box-shadow: 0 20px 46px rgba(14, 37, 79, 0.07);
        }

        .case-study-copy {
            display: grid;
            gap: 10px;
        }

        .case-study-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            max-width: 100%;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(217, 236, 255, 0.72);
            color: var(--ink-700);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .case-study-title {
            margin: 0;
            color: var(--ink-900);
            font-size: clamp(22px, 1.6vw, 30px);
            line-height: 1.12;
            letter-spacing: -.045em;
        }

        .case-study-media {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            border: 1px solid rgba(199, 219, 238, 0.86);
            aspect-ratio: 4 / 5;
            background:
                radial-gradient(circle at top right, rgba(140, 217, 255, 0.3) 0%, transparent 30%),
                linear-gradient(160deg, rgba(234, 242, 255, 0.98) 0%, rgba(247, 250, 255, 0.98) 100%);
        }

        .case-study-media img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .case-study-placeholder {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 22% 18%, rgba(255, 162, 74, 0.24) 0%, transparent 26%),
                radial-gradient(circle at 78% 82%, rgba(139, 217, 255, 0.28) 0%, transparent 26%),
                linear-gradient(145deg, rgba(255, 255, 255, 0.78) 0%, rgba(227, 238, 253, 0.92) 100%);
        }

        .case-study-placeholder span {
            width: 78%;
            aspect-ratio: 1;
            border-radius: 28px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.82);
            color: var(--ink-900);
            box-shadow:
                inset 0 0 0 1px rgba(255, 255, 255, 0.94),
                0 22px 42px rgba(14, 37, 79, 0.08);
            font-size: clamp(32px, 2.6vw, 48px);
        }

        .case-study-excerpt {
            margin: 0;
            color: var(--ink-500);
            font-size: 15px;
            line-height: 1.66;
        }

        .case-study-btn {
            margin-top: auto;
            align-self: flex-start;
            min-width: 148px;
            justify-content: center;
            text-decoration: none;
        }

        .products-grid {
            margin-top: 32px;
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .product-card {
            border-radius: 22px;
            border: 1px solid var(--line);
            background: var(--surface);
            padding: 0;
            overflow: hidden;
            box-shadow: 0 12px 26px rgba(14, 37, 79, 0.06);
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .product-image {
            height: 220px;
            width: 100%;
            object-fit: cover;
            display: block;
            border-radius: 0;
            background: var(--surface-soft);
        }

        .product-body {
            padding: 16px 18px 18px;
            text-align: left;
            display: flex;
            flex: 1;
            flex-direction: column;
        }

        .product-name {
            margin: 0;
            font-size: 18px;
            line-height: 1.22;
            font-weight: 700;
            color: var(--ink-900);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-desc {
            margin: 8px 0 0;
            color: var(--ink-500);
            font-size: 14px;
            line-height: 1.48;
            min-height: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-bottom {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: nowrap;
            padding-top: 14px;
        }

        .price {
            font-size: 16px;
            font-weight: 700;
            color: var(--ink-900);
        }

        .view-btn {
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .discover {
            background: transparent;
        }

        .discover-grid {
            display: grid;
            grid-template-columns: .92fr 1.08fr;
            gap: 42px;
            align-items: center;
            padding: 46px;
            border-radius: 40px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(236, 246, 255, 0.92) 0%, rgba(255, 255, 255, 0.98) 100%);
            box-shadow: var(--shadow-soft);
        }

        .discover-list {
            margin: 26px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 14px;
            color: var(--ink-700);
            font-size: 18px;
        }

        .discover-list li i {
            color: var(--orange);
            margin-right: 10px;
        }

        .video-wrap {
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-card);
            background: #0c1425;
        }

        .video-wrap iframe {
            width: 100%;
            height: 400px;
            border: 0;
            display: block;
        }

        .pill {
            margin-bottom: 16px;
        }

        .services-grid {
            margin-top: 34px;
            display: grid;
            gap: 22px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .service-card {
            border-radius: 30px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            padding: 30px;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
        }

        .service-card h3 {
            margin: 0;
            font-size: 23px;
            line-height: 1.24;
        }

        .service-card p {
            margin: 16px 0 0;
            font-size: 16px;
            line-height: 1.66;
            color: var(--ink-500);
            flex: 1;
        }

        .service-card .btn-orange {
            margin-top: 20px;
            align-self: flex-start;
            width: auto;
            justify-content: center;
            padding: 12px 22px;
            font-size: 15px;
            box-shadow: 0 12px 24px rgba(255, 155, 47, 0.18);
        }

        .career-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.04fr) minmax(0, .96fr);
            gap: 28px;
            align-items: start;
        }

        .career-title {
            margin: 0 0 22px;
            font-size: clamp(28px, 2.15vw, 38px);
            letter-spacing: -.035em;
        }

        .job-list {
            display: grid;
            gap: 18px;
        }

        .job-item {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 22px 24px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 18px;
            align-items: center;
            box-shadow: var(--shadow-card);
        }

        .job-icon {
            font-size: 26px;
            color: var(--orange-dark);
            width: 50px;
            height: 50px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: var(--orange-soft);
        }

        .job-title {
            margin: 0;
            font-size: 20px;
            line-height: 1.24;
        }

        .job-desc {
            margin: 10px 0 0;
            color: var(--ink-500);
            font-size: 16px;
            line-height: 1.6;
        }

        .apply-btn {
            border: 1px solid var(--line);
            color: var(--ink-900);
            border-radius: 999px;
            background: var(--surface-soft-2);
            font-weight: 700;
            padding: 13px 22px;
            font-size: 15px;
            box-shadow: 0 10px 22px rgba(14, 37, 79, 0.04);
        }

        .apply-panel {
            background: linear-gradient(180deg, rgba(236, 246, 255, 0.94) 0%, #ffffff 100%);
            border: 1px solid var(--line);
            border-radius: 32px;
            padding: 30px;
            box-shadow: var(--shadow-soft);
        }

        .apply-panel form {
            display: grid;
            gap: 14px;
        }

        .field,
        .field-select,
        .field-file {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px 18px;
            font-size: 17px;
            font-family: inherit;
            width: 100%;
            background: rgba(255, 255, 255, 0.94);
            color: var(--ink-900);
        }

        .field-file {
            padding: 10px;
        }

        .field:focus,
        .field-select:focus,
        .field-file:focus {
            outline: none;
            border-color: rgba(255, 155, 47, 0.45);
            box-shadow: 0 0 0 4px rgba(255, 155, 47, 0.1);
        }

        .resume-label {
            font-size: 15px;
            font-weight: 600;
            color: var(--ink-500);
            margin-top: 2px;
        }

        .field-submit {
            margin-top: 8px;
            border: 0;
            border-radius: 999px;
            padding: 16px 24px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
        }

        .guide-stack {
            display: grid;
            gap: 16px;
        }

        .guide-intro {
            margin: 0;
            color: var(--ink-500);
            font-size: 17px;
            line-height: 1.62;
        }

        .guide-list {
            margin: 0;
            padding-left: 22px;
            color: var(--ink-700);
            display: grid;
            gap: 10px;
            font-size: 16px;
            line-height: 1.62;
        }

        .guide-actions {
            display: grid;
            gap: 12px;
            margin-top: 4px;
        }

        .guide-actions .field-submit {
            margin-top: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .article-wrap {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(214, 226, 240, 0.92);
            border-radius: 38px;
            background:
                radial-gradient(circle at top right, rgba(255, 187, 109, 0.16) 0%, rgba(255, 187, 109, 0) 22%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(247, 251, 255, 0.98) 100%);
            padding: 44px 42px;
            box-shadow: 0 24px 58px rgba(15, 37, 79, 0.08);
        }

        .article-wrap::before {
            content: '';
            position: absolute;
            left: 18px;
            top: 34px;
            bottom: 34px;
            width: 5px;
            border-radius: 999px;
            background: linear-gradient(180deg, var(--orange) 0%, #ffb75f 100%);
        }

        .article-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88);
        }

        .article-box {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(225, 234, 244, 0.98);
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.98);
            padding: 68px 72px 60px;
            box-shadow: 0 16px 38px rgba(15, 37, 79, 0.06);
        }

        .article-box::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(222, 232, 242, 0.55) 0%, rgba(222, 232, 242, 0.85) 38%, rgba(222, 232, 242, 0.55) 100%);
        }

        .article-box::after {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            top: -150px;
            right: -120px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 155, 47, 0.11) 0%, rgba(255, 155, 47, 0) 74%);
        }

        .article-box > * {
            position: relative;
            z-index: 1;
        }

        .article-box h1,
        .article-box h2,
        .article-box h3 {
            margin: 0;
            position: relative;
            color: var(--ink-900);
            font-weight: 800;
        }

        .article-box h1 {
            padding-bottom: 24px;
            font-size: clamp(38px, 3.1vw, 56px);
            line-height: 1.06;
            letter-spacing: -.045em;
        }

        .article-box h2 {
            padding-bottom: 22px;
            font-size: clamp(30px, 2.35vw, 42px);
            line-height: 1.12;
            letter-spacing: -.04em;
            text-align: left;
        }

        .article-box > h1:first-child,
        .article-box > h2:first-child {
            font-size: clamp(40px, 3.5vw, 58px);
            line-height: 1.08;
            max-width: 20ch;
            margin-inline: auto;
            text-align: center;
        }

        .article-box h1::after,
        .article-box h2::after,
        .article-box h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 72px;
            height: 4px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--orange-dark) 0 56%, var(--sky-strong) 56% 100%);
        }

        .article-box h2::after {
            left: 0;
            transform: none;
        }

        .article-box > h1:first-child::after,
        .article-box > h2:first-child::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .article-box p {
            margin: 0;
            font-size: 18px;
            color: var(--ink-500);
            line-height: 1.78;
        }

        .article-box h3 {
            padding-bottom: 18px;
            font-size: clamp(22px, 1.75vw, 30px);
            line-height: 1.2;
            letter-spacing: -.03em;
        }

        .article-box ul,
        .article-box ol {
            margin: 0;
            padding-left: 28px;
            color: var(--ink-500);
            font-size: 18px;
            line-height: 1.72;
            display: grid;
            gap: 10px;
        }

        .article-box ul li::marker,
        .article-box ol li::marker {
            color: var(--orange-dark);
            font-weight: 700;
        }

        .article-box hr {
            margin: 34px 0;
            border: 0;
            border-top: 1px solid rgba(224, 233, 242, 0.95);
        }

        .article-box h1 + p,
        .article-box h2 + p,
        .article-box > h2:first-child + p {
            font-size: clamp(19px, 1.45vw, 23px);
            line-height: 1.72;
            color: #3f5c82;
        }

        .article-box h3 + p {
            font-size: 18px;
            line-height: 1.72;
        }

        .article-box li + li {
            margin-top: 0;
        }

        .article-box * + h2,
        .article-box * + h3,
        .article-box * + ul,
        .article-box * + ol,
        .article-box * + p {
            margin-top: 22px;
        }

        .article-box * + h1 {
            margin-top: 10px;
        }

        .article-box * + h1,
        .article-box * + h3 {
            margin-top: 46px;
        }

        .article-box * + h2 {
            margin-top: 58px;
        }

        .article-box blockquote {
            margin: 28px 0 0;
            padding: 18px 22px;
            border-left: 4px solid var(--orange);
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255, 242, 223, 0.72) 0%, rgba(255, 255, 255, 0.96) 100%);
            color: var(--ink-700);
            font-size: 19px;
            line-height: 1.65;
            box-shadow: inset 0 0 0 1px rgba(255, 155, 47, 0.08);
        }

        .footer {
            border-top: 1px solid var(--line);
            color: var(--ink-500);
            text-align: center;
            font-size: 15px;
            padding: 28px 0 42px;
        }

        .whatsapp-wrap {
            position: fixed;
            right: 26px;
            bottom: 22px;
            z-index: 40;
            display: grid;
            gap: 10px;
            justify-items: end;
        }

        .whatsapp-tip {
            background: var(--ink-900);
            color: #fff;
            border-radius: 999px;
            padding: 10px 16px;
            font-size: 14px;
            box-shadow: 0 14px 24px rgba(13, 28, 61, 0.18);
        }

        .whatsapp-btn {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 0;
            display: grid;
            place-items: center;
            font-size: 34px;
            background: #25d366;
            color: #fff;
            box-shadow: 8px 8px 0 rgba(255, 155, 47, 0.88);
        }

        @media (max-width: 1200px) {
            .case-studies-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .products-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1120px) {
            .hero,
            .discover-grid,
            .career-grid {
                grid-template-columns: 1fr;
            }

            .hero {
                gap: 34px;
            }

            .hero h1,
            .hero p {
                max-width: none;
            }

            .hero-banner {
                min-height: 460px;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 980px) {
            .main-bar-inner {
                grid-template-columns: minmax(0, 1fr) auto;
                justify-items: stretch;
                gap: 18px;
                padding-bottom: 22px;
            }

            .brand-block {
                min-width: 0;
                justify-self: start;
            }

            .header-search {
                grid-column: 1 / -1;
                grid-row: 2;
            }

            .header-actions {
                justify-self: end;
            }

            .article-box {
                padding: 44px 38px;
            }
        }

        @media (max-width: 900px) {
            .case-studies-grid {
                grid-template-columns: 1fr;
            }

            .products-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .container {
                width: min(1320px, calc(100vw - 24px));
            }

            .home > .container:first-of-type {
                display: flex;
                flex-direction: column;
            }

            .store-header {
                position: sticky;
                top: 0;
                z-index: 30;
                box-shadow: 0 8px 18px rgba(14, 37, 79, 0.06);
            }

            .notice-bar {
                display: none;
            }

            .notice-inner {
                min-height: auto;
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 8px;
                padding: 7px 0 5px;
            }

            .notice-left {
                max-width: 100%;
                justify-content: center;
                font-size: 10px;
                padding: 8px 12px;
                letter-spacing: .08em;
            }

            .notice-right {
                width: 100%;
                justify-content: flex-start;
                gap: 8px;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .notice-right::-webkit-scrollbar,
            .section-nav::-webkit-scrollbar,
            .mobile-shop-rail::-webkit-scrollbar {
                display: none;
            }

            .notice-right a {
                white-space: nowrap;
                padding: 8px 12px;
                font-size: 12px;
            }

            .main-bar-inner {
                min-height: auto;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 9px 10px;
                padding: 8px 0 10px;
            }

            .brand-block {
                gap: 10px;
            }

            .brand-mark {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                font-size: 20px;
            }

            .brand-text strong {
                font-size: 19px;
            }

            .brand-text small {
                margin-top: 4px;
                font-size: 8px;
                letter-spacing: .14em;
            }

            .header-search {
                grid-column: 1 / -1;
                grid-row: 2;
                border-radius: 18px;
                box-shadow: 0 10px 22px rgba(14, 37, 79, 0.06);
            }

            .header-search input {
                height: 50px;
                padding: 0 14px;
                font-size: 14px;
            }

            .header-search button {
                min-width: 52px;
                padding: 0 14px;
            }

            .header-actions {
                gap: 7px;
                justify-self: end;
            }

            .header-icon,
            .chevron-icon {
                width: 40px;
                height: 40px;
                border-radius: 14px;
                font-size: 17px;
            }

            .section-nav {
                order: 1;
                flex-wrap: nowrap;
                overflow-x: auto;
                margin: 10px -12px 10px;
                padding: 8px 12px;
                border-radius: 0;
                border-width: 1px 0;
                background: rgba(255, 255, 255, 0.84);
                scrollbar-width: none;
            }

            .section-nav a {
                white-space: nowrap;
                padding: 10px 13px;
                font-size: 13px;
            }

            .mobile-shop-rail {
                order: 2;
                display: grid;
                grid-auto-flow: column;
                grid-auto-columns: minmax(86px, 1fr);
                gap: 8px;
                margin: 0 -12px 6px;
                padding: 0 12px 8px;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .mobile-shop-tile {
                min-height: 76px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 8px 16px rgba(14, 37, 79, 0.05);
                display: grid;
                align-content: center;
                justify-items: center;
                gap: 7px;
                padding: 10px 7px;
                color: var(--ink-700);
                font-size: 12px;
                font-weight: 800;
                text-align: center;
                line-height: 1.2;
            }

            .mobile-shop-tile i {
                width: 32px;
                height: 32px;
                border-radius: 11px;
                display: grid;
                place-items: center;
                background: var(--orange-soft);
                color: var(--orange-dark);
                font-size: 16px;
            }

            .hero {
                order: 3;
                grid-template-columns: minmax(0, 1fr) 118px;
                align-items: center;
                gap: 14px;
                padding: 10px 0 18px;
            }

            .hero > div {
                min-width: 0;
            }

            #packages {
                order: 4;
            }

            #case-studies {
                order: 5;
            }

            .hero-welcome-badge,
            .pill {
                padding: 8px 12px;
                font-size: 10px;
                letter-spacing: .08em;
            }

            .hero h1 {
                margin-top: 8px;
                font-size: 22px;
                line-height: 1.08;
                letter-spacing: -.03em;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .hero p {
                display: none;
            }

            .hero p,
            .section-intro,
            .article-box p,
            .article-box ul,
            .article-box ol {
                font-size: 16px;
            }

            .btn {
                width: auto;
                min-height: 42px;
                padding: 11px 15px;
                font-size: 14px;
                justify-content: center;
            }

            .hero-actions {
                width: 100%;
                gap: 8px;
                margin-top: 12px;
            }

            .hero-banner {
                min-height: 132px;
                border-radius: 18px;
                box-shadow:
                    4px 4px 0 #22384c,
                    0 12px 24px rgba(14, 37, 79, 0.1);
            }

            .hero-banner-image {
                inset: 10px;
                width: calc(100% - 20px);
                height: calc(100% - 20px);
                border-radius: 15px;
            }

            .dish-shape {
                inset: 28px 24px;
            }

            .section {
                padding: 30px 0;
            }

            .section-title,
            .career-title {
                font-size: 22px;
                letter-spacing: -.025em;
            }

            #packages .section-intro {
                margin-top: 8px;
            }

            .products-grid {
                margin-top: 14px;
                gap: 8px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .case-study-card {
                padding: 16px;
                border-radius: 24px;
            }

            .case-study-media {
                border-radius: 20px;
            }

            .case-study-btn {
                width: 100%;
            }

            .product-image {
                height: 118px;
            }

            .product-body {
                padding: 9px;
            }

            .product-name {
                font-size: 13px;
                line-height: 1.25;
            }

            .product-desc {
                display: none;
            }

            .product-bottom {
                display: grid;
                gap: 7px;
                padding-top: 9px;
            }

            .price {
                font-size: 15px;
            }

            .view-btn {
                width: 100%;
                padding: 8px 10px;
                font-size: 12px;
                text-align: center;
            }

            .discover-grid,
            .apply-panel,
            .article-wrap,
            .article-box {
                padding: 20px;
                border-radius: 24px;
            }

            .service-card {
                padding: 20px;
                border-radius: 22px;
            }

            .service-card .btn-orange,
            .case-study-btn,
            .field-submit,
            .guide-actions .btn-outline {
                width: 100%;
            }

            .article-wrap::before {
                left: 12px;
                top: 24px;
                bottom: 24px;
            }

            .article-box h1,
            .article-box h2 {
                font-size: clamp(30px, 8vw, 42px);
                padding-bottom: 20px;
            }

            .article-box h3 {
                font-size: clamp(24px, 6.2vw, 34px);
                padding-bottom: 18px;
            }

            .article-box p,
            .article-box ul,
            .article-box ol,
            .article-box h1 + p,
            .article-box h2 + p,
            .article-box h3 + p {
                font-size: 16px;
                line-height: 1.72;
            }

            .job-item {
                grid-template-columns: 1fr;
                justify-items: start;
            }

            .video-wrap iframe {
                height: 220px;
            }

            .whatsapp-wrap {
                right: 16px;
                bottom: 16px;
            }

            .whatsapp-btn {
                width: 54px;
                height: 54px;
                font-size: 26px;
                box-shadow: 5px 5px 0 rgba(255, 155, 47, 0.84);
            }
        }

        @media (max-width: 420px) {
            .brand-text strong {
                font-size: 18px;
            }

            .brand-text small {
                font-size: 7px;
            }

            .header-icon,
            .chevron-icon {
                width: 38px;
                height: 38px;
                border-radius: 13px;
                font-size: 16px;
            }

            .hero h1 {
                font-size: 21px;
            }

            .products-grid {
                gap: 7px;
            }

            .product-image {
                height: 108px;
            }
        }
    </style>

    <main class="home">
        <header class="store-header">
            <div class="notice-bar">
                <div class="container notice-inner">
                    <div class="notice-left">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span>Nationwide delivery and installation support</span>
                    </div>
                    <div class="notice-right">
                        <a href="#prices" aria-label="View Starlink Kenya prices">
                            <i class="fa-solid fa-tags"></i>
                            Prices
                        </a>
                        <a href="{{ $primaryPhoneHref }}" aria-label="Call {{ $primaryPhone }}">
                            <i class="fa-solid fa-headset"></i>
                            {{ $primaryPhone }}
                        </a>
                        <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-bar">
                <div class="container main-bar-inner">
                    <a class="brand-block" href="{{ route('home') }}" aria-label="Starlink Kenya home">
                        <span class="brand-mark"><i class="fa-solid fa-satellite-dish"></i></span>
                        <span class="brand-text">
                            <strong>STARLINK</strong>
                            <small>KENYA INSTALLERS</small>
                        </span>
                    </a>
                    <form class="header-search" action="{{ route('home') }}#packages" method="GET" role="search">
                        <input type="search" name="q" value="{{ $searchQuery ?? request('q') }}" placeholder="Search Starlink kits, mounts, and accessories" aria-label="Search Starlink products">
                        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                    <div class="header-actions">
                        <a class="header-icon" href="{{ $whatsappHref }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a class="header-icon" href="{{ route('shop.cart.index') }}" aria-label="Cart">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="cart-badge">{{ $cartCount }}</span>
                        </a>
                        <a class="header-icon" href="{{ $primaryPhoneHref }}" aria-label="Call {{ $primaryPhone }}">
                            <i class="fa-solid fa-phone"></i>
                        </a>
                        <a class="header-icon chevron-icon" href="{{ $accountEntryUrl }}" aria-label="Account menu">
                            <i class="fa-solid fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <div class="container">
            <nav class="section-nav" aria-label="Page section navigation">
                @foreach ($homeNavigationMenu as $menuItem)
                    <a href="{{ $menuItem['href'] }}">{{ $menuItem['label'] }}</a>
                @endforeach
            </nav>

            <div class="mobile-shop-rail" aria-label="Popular Starlink actions">
                <a class="mobile-shop-tile" href="#packages">
                    <i class="fa-solid fa-box-open"></i>
                    Kits
                </a>
                <a class="mobile-shop-tile" href="#prices">
                    <i class="fa-solid fa-tags"></i>
                    Prices
                </a>
                <a class="mobile-shop-tile" href="#installation">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    Install
                </a>
                <a class="mobile-shop-tile" href="{{ $whatsappHref }}" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i>
                    Quote
                </a>
            </div>

            <section class="hero">
                <div>
                    <span class="hero-welcome-badge">Welcome to Starlink Kenya</span>
                    <h1>{{ $heroTitle }}</h1>
                    <p>{{ $heroDescription }}</p>
                    <div class="hero-actions">
                        <a class="btn btn-orange" href="#packages">Shop Now</a>
                        <a class="btn btn-outline" href="{{ $primaryPhoneHref }}">Talk to an Expert</a>
                    </div>
                </div>
                <div class="hero-banner {{ $heroImageUrl ? 'has-image' : '' }}" aria-hidden="true">
                    @if ($heroImageUrl)
                        <img class="hero-banner-image" src="{{ $heroImageUrl }}" alt="Starlink Kenya Hero">
                    @else
                        <div class="dish-shape"></div>
                    @endif
                </div>
            </section>

            @if ($caseStudies->isNotEmpty())
                <section id="case-studies" class="section case-studies-section">
                    <div class="case-studies-head">
                        <span class="case-studies-kicker">Case Studies</span>
                        <div>
                            <h2 class="section-title">{{ $caseStudiesHeadline }}</h2>
                            <p class="section-intro">{{ $caseStudiesIntro }}</p>
                        </div>
                    </div>

                    <div class="case-studies-grid">
                        @foreach ($caseStudies as $caseStudy)
                            <article class="case-study-card">
                                <div class="case-study-copy">
                                    <span class="case-study-label">{{ $caseStudy['label'] }}</span>
                                    <h3 class="case-study-title">{{ $caseStudy['title'] }}</h3>
                                </div>

                                <div class="case-study-media">
                                    <div class="case-study-placeholder" aria-hidden="true">
                                        <span><i class="fa-solid fa-satellite-dish"></i></span>
                                    </div>

                                    @if (! empty($caseStudy['image_url']))
                                        <img src="{{ $caseStudy['image_url'] }}" alt="{{ $caseStudy['image_alt'] }}" loading="lazy" decoding="async">
                                    @endif
                                </div>

                                <p class="case-study-excerpt">{{ $caseStudy['excerpt'] }}</p>
                                <a class="btn btn-orange case-study-btn" href="{{ $caseStudy['href'] }}">Read more</a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="packages" class="section">
                <span id="kits" class="section-anchor" aria-hidden="true"></span>
                <h2 class="section-title">{{ $productsTitle }}</h2>
                <p class="section-intro">
                    @if (! empty($searchQuery))
                        Search results for "{{ $searchQuery }}".
                    @else
                        Explore our genuine Starlink hardware and accessories.
                    @endif
                </p>
                <div class="products-grid">
                    @forelse ($products as $product)
                        <article class="product-card">
                            <img
                                class="product-image"
                                src="{{ $product->image_url ?: $productFallbackImage }}"
                                alt="{{ $product->name }}"
                                loading="lazy"
                                decoding="async"
                                onerror="this.onerror=null;this.src='{{ $productFallbackImage }}';">
                            <div class="product-body">
                                <h3 class="product-name">{{ $product->name }}</h3>
                                <p class="product-desc">
                                    {{ $product->name }} delivers reliable low-latency satellite internet across Kenya.
                                </p>
                                <div class="product-bottom">
                                    <span class="price">KES {{ number_format((float) $product->price, 2) }}</span>
                                    <a class="view-btn" href="{{ route('shop.product.show', ['productSlug' => $product->slug ?: $product->id]) }}">View</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="product-card">
                            <div class="product-body">
                                @if (! empty($searchQuery))
                                    <h3 class="product-name">No products matched your search</h3>
                                    <p class="product-desc">Try a different product name or <a href="{{ route('home') }}#packages">browse all kits</a>.</p>
                                @else
                                    <h3 class="product-name">No products published yet</h3>
                                    <p class="product-desc">Login to the admin dashboard and add Starlink kits.</p>
                                @endif
                            </div>
                        </article>
                    @endforelse
                </div>
            </section>
        </div>

        <section id="coverage" class="section discover">
            <div class="container">
                <div class="discover-grid">
                    <div>
                        <h2 class="section-title">{{ $whyChooseTitle }}</h2>
                        <p class="section-intro">{{ $whyChooseDescription }}</p>
                        <ul class="discover-list">
                            <li><i class="fa-solid fa-check"></i> Speeds up to 220 Mbps</li>
                            <li><i class="fa-solid fa-check"></i> Optimized for Kenyan terrain</li>
                            <li><i class="fa-solid fa-check"></i> DIY or Pro install</li>
                        </ul>
                        <div class="hero-actions">
                            <a class="btn btn-orange" href="#installation">Get Started</a>
                        </div>
                    </div>
                    <div class="video-wrap">
                        <iframe
                            src="{{ $coverageVideoEmbedUrl }}"
                            title="{{ $whyChooseTitle }} video"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </section>

        <section id="installation" class="section">
            <span id="services" class="section-anchor" aria-hidden="true"></span>
            <div class="container">
                <div style="text-align:center;">
                    <span class="pill">Our Services</span>
                    <h2 class="section-title">Explore Our Starlink Kenya Solutions</h2>
                </div>
                <div class="services-grid">
                    @foreach ($serviceCards as $service)
                        <article class="service-card">
                            <h3>{{ $service['title'] }}</h3>
                            <p>{{ $service['desc'] }}</p>
                            <a class="btn btn-orange" href="{{ $service['href'] }}">{{ $service['cta'] }}</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="prices" class="section">
            <span id="order-now" class="section-anchor" aria-hidden="true"></span>
            <div class="container">
                <div class="career-grid">
                    <div>
                        <h2 class="career-title">Starlink Kenya Packages Snapshot</h2>
                        <div class="job-list">
                            @foreach ($packageHighlights as $highlight)
                                <article class="job-item">
                                    <i class="fa-solid {{ $highlight['icon'] }} job-icon"></i>
                                    <div>
                                        <h3 class="job-title">{{ $highlight['title'] }}</h3>
                                        <p class="job-desc">{{ $highlight['desc'] }}</p>
                                    </div>
                                    <a class="apply-btn" href="{{ $highlight['href'] }}">{{ $highlight['cta'] }}</a>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h2 class="career-title">How to Order Starlink in Kenya</h2>
                        <div class="apply-panel">
                            <div class="guide-stack">
                                <p class="guide-intro">Get hardware, pricing, delivery, and installation support from one Kenya-based team.</p>
                                <ol class="guide-list">
                                    @foreach ($orderSteps as $step)
                                        <li>{{ $step }}</li>
                                    @endforeach
                                </ol>
                                <div class="guide-actions">
                                    <a class="field-submit" href="{{ $whatsappHref }}" target="_blank" rel="noopener">Request Quote on WhatsApp</a>
                                    <a class="btn btn-outline" href="{{ $primaryPhoneHref }}">Call {{ $primaryPhone }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="faqs" class="section">
            <div class="container">
                <div class="article-wrap">
                    <article class="article-box">
                        {!! $homePageContentHtml !!}
                    </article>
                </div>
            </div>
        </section>

        <footer class="footer">
            Copyright {{ now()->year }} Starlink Kenya. All rights reserved.
        </footer>

        <div class="whatsapp-wrap">
            <div class="whatsapp-tip">Chat with us on WhatsApp</div>
            <a class="whatsapp-btn" href="{{ $whatsappHref }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
        </div>
    </main>
@endsection
