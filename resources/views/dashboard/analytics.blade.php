@extends('layouts.app', ['title' => 'Website Analytics | Starlink Kenya'])

@php
    $rangeOptions = [
        7 => 'Last 7 days',
        30 => 'Last 30 days',
        90 => 'Last 90 days',
    ];
@endphp

@push('styles')
    <style>
        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 312px;
            background: #eef1f6;
            border-right: 1px solid #dbe1eb;
            padding: 12px 0 18px;
        }

        .main {
            flex: 1;
            padding: 18px 20px;
            background:
                radial-gradient(circle at top right, rgba(69, 133, 255, 0.1), transparent 30%),
                linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
        }

        .toolbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 12px;
            letter-spacing: .18em;
            color: #31527f;
            background: #e5eefb;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }

        .page-title {
            margin: 10px 0 0;
            font-size: 22px;
            letter-spacing: -0.03em;
            color: #172744;
        }

        .subtitle {
            margin: 5px 0 0;
            max-width: 52rem;
            color: #5f7391;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.5;
        }

        .range-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .range-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfd0e7;
            background: #f7faff;
            color: #48617f;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
        }

        .range-btn.active {
            background: linear-gradient(135deg, #1b74e8, #1356b8);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 14px 28px rgba(27, 116, 232, 0.24);
        }

        .breadcrumb {
            margin-top: 12px;
            font-size: 12px;
            color: #7a8da8;
            text-align: right;
        }

        .breadcrumb span {
            color: #1b74e8;
        }

        .stack {
            margin-top: 16px;
            display: grid;
            gap: 16px;
        }

        .status-panel,
        .panel,
        .summary-card {
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #dce5f1;
            box-shadow: 0 12px 28px rgba(21, 41, 72, 0.06);
        }

        .status-panel {
            padding: 18px;
        }

        .status-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .eyebrow {
            margin: 0;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: #7d8ea7;
        }

        .section-title {
            margin: 10px 0 0;
            font-size: 18px;
            line-height: 1.2;
            letter-spacing: -.04em;
            color: #172744;
        }

        .section-copy {
            margin: 6px 0 0;
            max-width: 54rem;
            color: #60738f;
            font-size: 13px;
            line-height: 1.6;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .badge,
        .badge-live {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .badge {
            background: #edf3fc;
            color: #516781;
            border: 1px solid #d7e2f0;
        }

        .badge-live {
            background: #e6f7ef;
            color: #0a8a5f;
            border: 1px solid #c7ebdb;
        }

        .summary-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .summary-card {
            padding: 16px;
        }

        .summary-label {
            margin: 0;
            color: #7489a5;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .summary-value {
            margin: 10px 0 0;
            font-size: 24px;
            line-height: 1;
            letter-spacing: -.05em;
            color: #10223f;
            font-weight: 900;
        }

        .summary-copy {
            margin: 8px 0 0;
            color: #60748f;
            font-size: 13px;
            line-height: 1.5;
        }

        .grid-two {
            display: grid;
            gap: 16px;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, .82fr);
        }

        .panel {
            padding: 18px;
        }

        .trend-chart {
            margin-top: 16px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .trend-item {
            min-width: 32px;
            text-align: center;
        }

        .trend-bar-wrap {
            height: 164px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .trend-bar {
            width: 100%;
            border-radius: 12px 12px 4px 4px;
            background: linear-gradient(180deg, #60a8ff 0%, #1b74e8 100%);
            box-shadow: 0 8px 18px rgba(27, 116, 232, 0.18);
        }

        .trend-day {
            margin-top: 8px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #7a8ca6;
        }

        .trend-value {
            margin-top: 3px;
            font-size: 11px;
            color: #50657f;
        }

        .list-stack {
            margin-top: 14px;
            display: grid;
            gap: 10px;
        }

        .list-card,
        .conversion-card {
            border-radius: 12px;
            border: 1px solid #e2e9f3;
            background: #f9fbff;
            padding: 13px 14px;
        }

        .list-topline {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .list-title {
            margin: 0;
            font-size: 14px;
            line-height: 1.35;
            font-weight: 800;
            color: #10223f;
        }

        .list-link {
            display: inline-block;
            margin-top: 4px;
            color: #637892;
            font-size: 12px;
            word-break: break-all;
        }

        .list-link:hover {
            color: #1b74e8;
        }

        .list-metrics {
            margin-top: 8px;
            color: #647992;
            font-size: 12px;
            line-height: 1.5;
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            background: #e7f1ff;
            color: #1b67cc;
            padding: 6px 9px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
        }

        .table-wrap {
            margin-top: 14px;
            overflow-x: auto;
            border: 1px solid #e2e9f3;
            border-radius: 12px;
            background: #fff;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }

        .table th,
        .table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #edf2f8;
            vertical-align: top;
        }

        .table th {
            background: #f7faff;
            color: #405673;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .table td {
            color: #425773;
            font-size: 13px;
            line-height: 1.5;
        }

        .table tr:last-child td {
            border-bottom: 0;
        }

        .table-path {
            display: block;
            margin-top: 4px;
            color: #7286a1;
            font-size: 11px;
        }

        .right-stack {
            display: grid;
            gap: 16px;
        }

        .conversion-grid {
            margin-top: 14px;
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .conversion-label {
            margin: 0;
            color: #7a8da8;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .conversion-value {
            margin: 10px 0 0;
            font-size: 22px;
            line-height: 1;
            color: #10223f;
            font-weight: 900;
        }

        .conversion-copy {
            margin: 8px 0 0;
            color: #61748f;
            font-size: 12px;
            line-height: 1.45;
        }

        .empty-state {
            margin-top: 14px;
            border: 1px dashed #cfdae8;
            border-radius: 12px;
            background: #f8fbff;
            padding: 14px;
            color: #647891;
            font-size: 13px;
            line-height: 1.55;
        }

        @media (max-width: 1400px) {
            .summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 1200px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #dbe1eb;
            }

            .grid-two,
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 900px) {
            .toolbar,
            .status-row {
                flex-direction: column;
            }

            .range-actions,
            .breadcrumb {
                justify-content: flex-start;
                text-align: left;
            }

            .grid-two,
            .summary-grid,
            .conversion-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .main {
                padding: 16px;
            }

            .page-title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }

            .panel,
            .status-panel,
            .summary-card {
                padding: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="layout">
        @include('dashboard.partials.sidebar', ['activeSection' => 'analytics'])

        <main class="main">
            <div class="toolbar">
                <div>
                    <div class="chip">Website Analytics</div>
                    <h1 class="page-title">Track storefront traffic, product interest, and buyer actions.</h1>
                    <p class="subtitle">See which public pages attract visitors, where visits come from, what products get attention, and how that activity turns into enquiries and orders.</p>
                </div>

                <div>
                    <div class="range-actions">
                        @foreach ($rangeOptions as $range => $label)
                            <a class="range-btn {{ $report['range'] === $range ? 'active' : '' }}" href="{{ route('analytics.index', ['range' => $range]) }}">{{ $label }}</a>
                        @endforeach
                    </div>
                    <div class="breadcrumb"><span>Home</span> / Analytics</div>
                </div>
            </div>

            <div class="stack">
                <section class="status-panel">
                    <div class="status-row">
                        <div>
                            <p class="eyebrow">Tracking Status</p>
                            <h2 class="section-title">Traffic data is updating from real public visits.</h2>
                            <p class="section-copy">
                                This period includes {{ number_format($report['pageViews']) }} page views from {{ number_format($report['uniqueVisitors']) }} unique visitors across the homepage, product pages, cart, and published content, with buyer intent signals captured from cart and WhatsApp CTA clicks.
                            </p>
                        </div>

                        <div class="badges">
                            <span class="badge-live">Live</span>
                            <span class="badge">{{ $rangeOptions[$report['range']] ?? 'Last 30 days' }}</span>
                            <span class="badge">{{ $report['start']->format('d M Y') }} to {{ $report['end']->format('d M Y') }}</span>
                            @if ($report['firstTrackedVisit'])
                                <span class="badge">First visit {{ $report['firstTrackedVisit']->format('d M Y') }}</span>
                            @else
                                <span class="badge">Waiting for first visit</span>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="summary-grid">
                    <article class="summary-card">
                        <p class="summary-label">Page Views</p>
                        <p class="summary-value">{{ number_format($report['pageViews']) }}</p>
                        <p class="summary-copy">{{ number_format($report['trackedPages']) }} tracked public pages were viewed in the selected window.</p>
                    </article>

                    <article class="summary-card">
                        <p class="summary-label">Unique Visitors</p>
                        <p class="summary-value">{{ number_format($report['uniqueVisitors']) }}</p>
                        <p class="summary-copy">First-party visitor count derived from repeat browser visits to the public site.</p>
                    </article>

                    <article class="summary-card">
                        <p class="summary-label">Pages Per Visitor</p>
                        <p class="summary-value">{{ number_format($report['pagesPerVisitor'], 1) }}</p>
                        <p class="summary-copy">Average page depth across the selected traffic window.</p>
                    </article>

                    <article class="summary-card">
                        <p class="summary-label">Product Views</p>
                        <p class="summary-value">{{ number_format($report['productViews']) }}</p>
                        <p class="summary-copy">Visits that landed on individual Starlink product detail pages.</p>
                    </article>

                    <article class="summary-card">
                        <p class="summary-label">WhatsApp Clicks</p>
                        <p class="summary-value">{{ number_format($report['whatsappClicks']) }}</p>
                        <p class="summary-copy">{{ number_format($report['whatsappProductClicks']) }} product order clicks / {{ number_format($report['whatsappCartClicks']) }} cart checkout clicks.</p>
                    </article>

                    <article class="summary-card">
                        <p class="summary-label">Lead Actions</p>
                        <p class="summary-value">{{ number_format($report['leadActions']) }}</p>
                        <p class="summary-copy">{{ number_format($report['cartActions']) }} cart adds / {{ number_format($report['whatsappClicks']) }} WhatsApp clicks / {{ number_format($report['conversions']['enquiries']) }} enquiries.</p>
                    </article>
                </section>

                <section class="grid-two">
                    <article class="panel">
                        <p class="eyebrow">Traffic Trend</p>
                        <h2 class="section-title">Daily page-view momentum.</h2>
                        <p class="section-copy">This chart reflects first-party public traffic only, using the visits captured by the Starlink Kenya storefront.</p>

                        <div class="trend-chart">
                            @foreach ($report['trend'] as $point)
                                <div class="trend-item">
                                    <div class="trend-bar-wrap">
                                        <div class="trend-bar" style="height: {{ $point['height'] }}px" title="{{ $point['tooltip'] }}"></div>
                                    </div>
                                    <p class="trend-day">{{ $point['dayLabel'] }}</p>
                                    <p class="trend-value">{{ $point['views'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="panel">
                        <p class="eyebrow">Top Pages</p>
                        <h2 class="section-title">The pages drawing the most attention.</h2>

                        @if ($report['topPages']->isNotEmpty())
                            <div class="list-stack">
                                @foreach ($report['topPages'] as $page)
                                    <article class="list-card">
                                        <div class="list-topline">
                                            <div>
                                                <h3 class="list-title">{{ $page['label'] }}</h3>
                                                <a class="list-link" href="{{ url($page['path']) }}" target="_blank" rel="noreferrer">{{ $page['path'] }}</a>
                                            </div>
                                            <span class="metric-pill">{{ number_format($page['views']) }} views</span>
                                        </div>
                                        <p class="list-metrics">
                                            {{ number_format($page['visitors']) }} visitors / {{ ucfirst(str_replace('-', ' ', $page['pageType'] ?? 'page')) }} / Last seen {{ $page['lastSeen']->format('d M Y H:i') }}
                                        </p>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">No public page visits have been tracked yet. Visit the homepage or a product page to start populating this panel.</div>
                        @endif
                    </article>
                </section>

                <section class="grid-two">
                    <article class="panel">
                        <p class="eyebrow">Recent Visits</p>
                        <h2 class="section-title">Latest recorded public page views.</h2>

                        @if ($report['recentVisits']->isNotEmpty())
                            <div class="table-wrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Page</th>
                                            <th>Referrer</th>
                                            <th>Visited</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($report['recentVisits'] as $visit)
                                            <tr>
                                                <td>
                                                    {{ $visit['label'] }}
                                                    <span class="table-path">{{ $visit['path'] }}</span>
                                                </td>
                                                <td>{{ $visit['referrer'] }}</td>
                                                <td>{{ $visit['occurredAt']->format('d M Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">Recent visits will appear here once the public storefront starts receiving traffic.</div>
                        @endif
                    </article>

                    <article class="panel">
                        <p class="eyebrow">Top Referrers</p>
                        <h2 class="section-title">External websites sending traffic.</h2>

                        @if ($report['topReferrers']->isNotEmpty())
                            <div class="list-stack">
                                @foreach ($report['topReferrers'] as $referrer)
                                    <article class="list-card">
                                        <div class="list-topline">
                                            <h3 class="list-title">{{ $referrer['host'] }}</h3>
                                            <span class="metric-pill">{{ number_format($referrer['views']) }} views</span>
                                        </div>
                                        <p class="list-metrics">{{ number_format($referrer['visitors']) }} unique visitors from this source.</p>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">No external referrers have been captured yet. Direct visits and internal navigation are excluded from this list.</div>
                        @endif
                    </article>
                </section>

                <section class="grid-two">
                    <article class="panel">
                        <p class="eyebrow">Top Product Pages</p>
                        <h2 class="section-title">Which products visitors explore most.</h2>
                        <p class="section-copy">Product detail traffic is useful for spotting commercial intent before an order is created.</p>

                        @if ($report['topProducts']->isNotEmpty())
                            <div class="list-stack">
                                @foreach ($report['topProducts'] as $product)
                                    <article class="list-card">
                                        <div class="list-topline">
                                            <div>
                                                <h3 class="list-title">{{ $product['label'] }}</h3>
                                                <a class="list-link" href="{{ url($product['path']) }}" target="_blank" rel="noreferrer">{{ $product['path'] }}</a>
                                            </div>
                                            <span class="metric-pill">{{ number_format($product['views']) }} views</span>
                                        </div>
                                        <p class="list-metrics">{{ number_format($product['visitors']) }} visitors / Last seen {{ $product['lastSeen']->format('d M Y H:i') }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">No product-page visits are in the selected window yet.</div>
                        @endif
                    </article>

                    <div class="right-stack">
                        <article class="panel">
                            <p class="eyebrow">Search Intent</p>
                            <h2 class="section-title">What visitors search for on the homepage.</h2>

                            @if ($report['topSearches']->isNotEmpty())
                                <div class="list-stack">
                                    @foreach ($report['topSearches'] as $search)
                                        <article class="list-card">
                                            <div class="list-topline">
                                                <h3 class="list-title">{{ $search['query'] }}</h3>
                                                <span class="metric-pill">{{ number_format($search['searches']) }} searches</span>
                                            </div>
                                            <p class="list-metrics">{{ number_format($search['visitors']) }} visitors / Last searched {{ $search['lastSeen']->format('d M Y H:i') }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">No tracked search queries yet. Homepage searches will appear here automatically.</div>
                            @endif
                        </article>

                        <article class="panel">
                            <p class="eyebrow">Conversion Signals</p>
                            <h2 class="section-title">Real outcomes from existing store tables.</h2>
                            <p class="section-copy">These numbers combine tracked public CTA clicks with the orders, invoices, enquiries, and payment timestamps already stored in the application.</p>

                            <div class="conversion-grid">
                                <article class="conversion-card">
                                    <p class="conversion-label">Product WhatsApp</p>
                                    <p class="conversion-value">{{ number_format($report['whatsappProductClicks']) }}</p>
                                    <p class="conversion-copy">Direct product order clicks sent to WhatsApp.</p>
                                </article>

                                <article class="conversion-card">
                                    <p class="conversion-label">Cart WhatsApp</p>
                                    <p class="conversion-value">{{ number_format($report['whatsappCartClicks']) }}</p>
                                    <p class="conversion-copy">Checkout clicks from the cart summary CTA.</p>
                                </article>

                                <article class="conversion-card">
                                    <p class="conversion-label">Orders</p>
                                    <p class="conversion-value">{{ number_format($report['conversions']['orders']) }}</p>
                                    <p class="conversion-copy">Orders created during the selected period.</p>
                                </article>

                                <article class="conversion-card">
                                    <p class="conversion-label">Paid Orders</p>
                                    <p class="conversion-value">{{ number_format($report['conversions']['paidOrders']) }}</p>
                                    <p class="conversion-copy">Orders with a recorded payment timestamp.</p>
                                </article>

                                <article class="conversion-card">
                                    <p class="conversion-label">Invoices</p>
                                    <p class="conversion-value">{{ number_format($report['conversions']['invoices']) }}</p>
                                    <p class="conversion-copy">Invoices issued inside the selected window.</p>
                                </article>

                                <article class="conversion-card">
                                    <p class="conversion-label">Enquiries</p>
                                    <p class="conversion-value">{{ number_format($report['conversions']['enquiries']) }}</p>
                                    <p class="conversion-copy">Visitor contact and sales enquiries received.</p>
                                </article>

                                <article class="conversion-card" style="grid-column: 1 / -1;">
                                    <p class="conversion-label">Revenue</p>
                                    <p class="conversion-value">KSh {{ number_format((float) $report['conversions']['revenue'], 2) }}</p>
                                    <p class="conversion-copy">Revenue collected from paid orders during the selected period.</p>
                                </article>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </main>
    </div>
@endsection
