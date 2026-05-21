@extends('layouts.app', ['title' => 'Dashboard | Starlink Kenya'])

@section('content')
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

        .brand {
            display: block;
            margin: 0 14px 14px;
            background: #f8fafd;
            border: 1px solid #d8dfec;
            border-radius: 16px;
            padding: 15px 18px;
            font-size: 18px;
            line-height: 1.15;
            font-weight: 800;
            color: #353f4e;
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        .menu-block {
            margin-bottom: 12px;
        }

        .menu-title {
            margin: 6px 16px 8px;
            font-size: 11px;
            font-weight: 600;
            color: #8b9ab1;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .menu-link,
        .menu-logout {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 0;
            padding: 10px 16px;
            border-radius: 0 12px 12px 0;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 700;
            color: #46556f;
        }

        .menu-link.active {
            background: linear-gradient(90deg, #3b74f3, #2f66f3);
            color: #fff;
            box-shadow: 0 12px 24px rgba(47, 102, 243, .22);
        }

        .menu-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #dce3ee;
            color: #62738f;
            font-size: 15px;
            flex-shrink: 0;
        }

        .menu-link.active .menu-icon {
            background: rgba(255, 255, 255, .2);
            color: #fff;
        }

        .menu-logout {
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .main {
            flex: 1;
            padding: 18px 20px;
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
            color: #3f587f;
            background: #e6edf8;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
        }

        .page-title {
            margin: 10px 0 0;
            font-size: 21px;
            letter-spacing: -0.03em;
            color: #1f2a3a;
        }

        .subtitle {
            margin: 4px 0 0;
            color: #586c89;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.45;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .action-btn {
            border: 1px solid #8d99ae;
            color: #4f607a;
            background: #f3f6fc;
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 700;
        }

        .action-btn.primary {
            background: #0f7bff;
            color: #fff;
            border-color: transparent;
        }

        .breadcrumb {
            margin-top: 12px;
            font-size: 12px;
            color: #7989a2;
            text-align: right;
        }

        .breadcrumb span {
            color: #0f7bff;
        }

        .stats-grid {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .stats-mini-grid {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            position: relative;
            border-radius: 14px;
            background: #fff;
            border: 1px solid #dce3ef;
            padding: 16px 18px;
            overflow: hidden;
        }

        .card::after {
            content: '';
            position: absolute;
            width: 112px;
            height: 112px;
            border-radius: 50%;
            top: -42px;
            right: -42px;
            opacity: .22;
        }

        .card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .card-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 17px;
        }

        .card-title {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            color: #516685;
            text-transform: uppercase;
        }

        .card-number {
            margin: 0 0 14px;
            font-size: 28px;
            line-height: 1;
            color: #0c1d3a;
        }

        .card-link {
            font-size: 14px;
            font-weight: 700;
        }

        .card.orders::after {
            background: #8aa8f7;
        }

        .card.orders .card-icon,
        .card.orders .card-link {
            color: #2f66f3;
            background: #dbe5ff;
        }

        .card.invoices::after {
            background: #88d3bb;
        }

        .card.invoices .card-icon,
        .card.invoices .card-link {
            color: #09946f;
            background: #d6f0e8;
        }

        .card.users::after {
            background: #f3c58a;
        }

        .card.users .card-icon,
        .card.users .card-link {
            color: #d97900;
            background: #f7ead8;
        }

        .card.enquiries::after {
            background: #f3b0c9;
        }

        .card.enquiries .card-icon,
        .card.enquiries .card-link {
            color: #ea124c;
            background: #f8e3ea;
        }

        .mini-card {
            border-radius: 14px;
            background: #fff;
            border: 1px solid #dce3ef;
            padding: 14px 16px;
            border-top: 4px solid transparent;
        }

        .mini-card h3 {
            margin: 0;
            font-size: 11px;
            color: #536987;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .mini-card .value {
            margin: 12px 0 4px;
            font-size: 25px;
            font-weight: 700;
            color: #0e1d39;
        }

        .mini-card .sub {
            color: #8293ab;
            font-size: 13px;
        }

        .mini-card.revenue {
            border-top-color: #505a68;
        }

        .mini-card.recent {
            border-top-color: #3f78f8;
        }

        .mini-card.new {
            border-top-color: #0db97f;
        }

        .mini-card.active {
            border-top-color: #ed9d00;
        }

        .bottom-panels {
            margin-top: 16px;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 16px;
        }

        .panel {
            border-radius: 14px;
            background: #fff;
            border: 1px solid #dce3ef;
            padding: 16px 18px;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .panel-title {
            margin: 0;
            font-size: 18px;
            line-height: 1.1;
            color: #132545;
        }

        .panel-sub {
            color: #7f90a8;
            font-size: 12px;
        }

        .activity-list {
            margin: 0;
            padding-left: 20px;
            color: #556a89;
            font-size: 14px;
            line-height: 1.55;
        }

        .quick-actions {
            display: grid;
            gap: 10px;
        }

        .quick-link {
            border: 1px solid #d4ddeb;
            background: #f6f9ff;
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 700;
            color: #425572;
            font-size: 14px;
        }

        @media (max-width: 1400px) {
            .menu-link,
            .menu-logout {
                padding: 12px 20px;
            }

            .menu-icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
            }

            .page-title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }

            .card-link,
            .mini-card .sub,
            .panel-title,
            .quick-link,
            .activity-list {
                font-size: 14px;
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

            .stats-grid,
            .stats-mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .bottom-panels {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .main {
                padding: 16px;
            }

            .toolbar {
                flex-direction: column;
            }

            .actions {
                justify-content: flex-start;
            }

            .breadcrumb {
                text-align: left;
            }

            .stats-grid,
            .stats-mini-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 20px;
            }

            .subtitle {
                font-size: 14px;
            }
        }
    </style>

    <div class="layout">
        @include('dashboard.partials.sidebar', ['activeSection' => 'dashboard'])

        <main class="main">
            <div class="toolbar">
                <div>
                    <div class="chip">Admin Overview</div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="subtitle">View and manage all customer orders</p>
                </div>

                <div>
                    <div class="actions">
                        <a class="action-btn primary" href="{{ route('admin.section', ['section' => 'invoices']) }}"><i class="fa-solid fa-plus"></i> New Invoice</a>
                        <a class="action-btn" href="{{ route('admin.section', ['section' => 'users']) }}"><i class="fa-solid fa-users"></i> Manage Users</a>
                        <a class="action-btn" href="{{ route('products.index') }}"><i class="fa-solid fa-gears"></i> Manage Products</a>
                    </div>
                    <div class="breadcrumb"><span>Home</span> / Dashboard</div>
                </div>
            </div>

            <section class="stats-grid">
                <article class="card orders">
                    <div class="card-head">
                        <div class="card-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                        <div class="card-title">Orders</div>
                    </div>
                    <p class="card-number">{{ $stats['orders'] }}</p>
                    <a class="card-link" href="{{ route('admin.section', ['section' => 'orders']) }}">View orders <i class="fa-solid fa-arrow-right"></i></a>
                </article>

                <article class="card invoices">
                    <div class="card-head">
                        <div class="card-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div class="card-title">Invoices</div>
                    </div>
                    <p class="card-number">{{ $stats['invoices'] }}</p>
                    <a class="card-link" href="{{ route('admin.section', ['section' => 'invoices']) }}">View invoices <i class="fa-solid fa-arrow-right"></i></a>
                </article>

                <article class="card users">
                    <div class="card-head">
                        <div class="card-icon"><i class="fa-solid fa-users"></i></div>
                        <div class="card-title">Users</div>
                    </div>
                    <p class="card-number">{{ $stats['users'] }}</p>
                    <a class="card-link" href="{{ route('admin.section', ['section' => 'users']) }}">View users <i class="fa-solid fa-arrow-right"></i></a>
                </article>

                <article class="card enquiries">
                    <div class="card-head">
                        <div class="card-icon"><i class="fa-solid fa-bell"></i></div>
                        <div class="card-title">Enquiries</div>
                    </div>
                    <p class="card-number">{{ $stats['enquiries'] }}</p>
                    <a class="card-link" href="{{ route('admin.section', ['section' => 'enquiries']) }}">View enquiries <i class="fa-solid fa-arrow-right"></i></a>
                </article>
            </section>

            <section class="stats-mini-grid">
                <article class="mini-card revenue">
                    <h3>Total Revenue</h3>
                    <div class="value">KSh {{ number_format((float) $stats['totalRevenue'], 2) }}</div>
                    <div class="sub">Paid orders</div>
                </article>

                <article class="mini-card recent">
                    <h3>Recent Orders</h3>
                    <div class="value">{{ $stats['recentOrders'] }}</div>
                    <div class="sub">Last 7 days</div>
                </article>

                <article class="mini-card new">
                    <h3>New Users</h3>
                    <div class="value">{{ $stats['newUsers'] }}</div>
                    <div class="sub">Last 30 days</div>
                </article>

                <article class="mini-card active">
                    <h3>Active Users</h3>
                    <div class="value">{{ $stats['activeUsers'] }}</div>
                    <div class="sub">Last 24 hours</div>
                </article>
            </section>

            <section class="bottom-panels">
                <article class="panel">
                    <div class="panel-head">
                        <h2 class="panel-title">Recent Activities</h2>
                        <span class="panel-sub">Latest updates</span>
                    </div>
                    <ol class="activity-list">
                        <li>New website deployment for Starlink Kenya.</li>
                        <li>20 customer orders migrated into dashboard records.</li>
                        <li>Inventory synced for Starlink kits and accessories.</li>
                    </ol>
                </article>

                <article class="panel">
                    <div class="panel-head">
                        <h2 class="panel-title">Quick Actions</h2>
                        <span class="panel-sub">Shortcuts</span>
                    </div>
                    <div class="quick-actions">
                        <a class="quick-link" href="{{ route('admin.section', ['section' => 'orders']) }}">Create order</a>
                        <a class="quick-link" href="{{ route('admin.section', ['section' => 'invoices']) }}">Create invoice</a>
                        <a class="quick-link" href="{{ route('products.index') }}">Add product</a>
                        <a class="quick-link" href="{{ route('admin.section', ['section' => 'enquiries']) }}">Manage enquiries</a>
                    </div>
                </article>
            </section>
        </main>
    </div>
@endsection
