<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $seo = $seo ?? [];
        $metaTitle = trim((string) ($seo['title'] ?? ($title ?? config('seo.site_name', 'Starlink Kenya'))));
        $metaDescription = \App\Support\SeoData::trimDescription($seo['description'] ?? config('seo.default_description'));
        $metaCanonical = trim((string) ($seo['canonical'] ?? url()->current()));
        $metaRobots = trim((string) ($seo['robots'] ?? 'index,follow'));
        $metaType = trim((string) ($seo['type'] ?? 'website'));
        $metaImage = $seo['image'] ?? null;
        $schemaItems = collect($seo['schema'] ?? [])
            ->filter(fn ($item): bool => is_array($item) && $item !== [])
            ->values();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $metaRobots }}">
    <link rel="canonical" href="{{ $metaCanonical }}">
    <meta property="og:locale" content="en_KE">
    <meta property="og:site_name" content="{{ config('seo.site_name', 'Starlink Kenya') }}">
    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $metaCanonical }}">
    @if ($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:card" content="{{ $metaImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if ($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @foreach ($schemaItems as $schemaItem)
        <script type="application/ld+json">{!! json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @endforeach
    @stack('head')
    @stack('styles')
</head>
<body class="min-h-screen font-sans antialiased">
    @yield('content')
</body>
</html>
